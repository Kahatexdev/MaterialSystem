<?php $this->extend($role . '/po/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <style>
        :root {
            --bg-page: #f8fafc;
            /* abu sangat muda */
            --bg-card: #ffffff;
            /* putih bersih */
            --bg-header: #3b82f6;
            /* biru soft */
            --text-header: #ffffff;
            --text-main: #1f2937;
            /* abu gelap nyaman */
            --text-muted: #6b7280;
            /* abu netral */
            --border-color: #e2e8f0;
            /* abu terang */
            --accent: #2563eb;
            /* biru aksen */
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            font-size: 15px;
            background-color: var(--bg-page);
            color: var(--text-main);
        }

        /* Card umum */
        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        /* Header card */
        .card-header {
            background-color: var(--bg-header) !important;
            color: var(--text-header) !important;
            font-size: 0.95rem;
            font-weight: 600;
            padding: .75rem 1rem;
        }

        /* Body card */
        .card-body {
            font-size: 0.9rem;
            line-height: 1.5;
            padding: 1rem;
        }

        /* Label */
        .form-label {
            font-weight: 500;
            color: var(--text-main);
            font-size: 0.85rem;
        }

        /* Input */
        .form-control {
            font-size: 0.9rem;
            border-radius: .5rem;
            border: 1px solid var(--border-color);
            padding: .45rem .75rem;
            color: var(--text-main);
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 .15rem rgba(37, 99, 235, 0.2);
        }

        /* Tombol */
        .btn {
            font-size: 0.85rem;
            border-radius: .5rem;
        }

        .btn-outline-danger {
            border-color: #ef4444;
            color: #ef4444;
        }

        .btn-outline-danger:hover {
            background: #ef4444;
            color: #fff;
        }

        /* Checkbox */
        .form-check-input {
            cursor: pointer;
            transform: scale(1.1);
        }

        /* Collapse toggle */
        .toggle-collapse i {
            transition: transform 0.2s ease;
        }

        .toggle-collapse[aria-expanded="true"] i {
            transform: rotate(180deg);
        }

        /* Selected card highlight */
        .card.is-selected {
            outline: 2px solid var(--accent);
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, 0.15);
        }
    </style>



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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>No PO</label>
                            <input type="text" class="form-control" name="no_model" value="" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Po Tambahan</label>
                            <select class="form-select" name="po_plus" id="po_plus">
                                <option value="">Pilih</option>
                                <option value="1">YA</option>
                                <option value="0">TIDAK</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5 mt-2">
                        <div class="form-group">
                            <label>Tanggal PO (Gudang Benang)</label>
                            <input type="date" class="form-control" name="tgl_po" id="tgl_po" value="" required>
                        </div>
                    </div>
                    <div class="col-md-5 mt-2">
                        <div class="form-group">
                            <label>Tanggal PO (Covering)</label>
                            <input type="date" class="form-control" name="tgl_po_covering" id="tgl_po_covering" value="" required>
                        </div>
                    </div>
                    <div class="col-md-2 mt-2">
                        <div class="form-check d-flex flex-column">
                            <label class="mb-1">Po Booking</label>
                            <input type="checkbox" class="form-check-input" id="po_booking" name="po_booking">
                        </div>
                    </div>

                </div>
                <!-- Search Card -->
                <div class="row align-items-center mb-3 mt-2">
                    <div class="col-md-12">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="searchCard" class="form-control border-start-0" placeholder="Ketik No Model atau PO atau Item Type...">
                        </div>
                        <small class="text-muted">Gunakan untuk mencari card berdasarkan no model atau PO atau item type.</small>
                    </div>
                </div>


                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label" for="selectAll">Pilih semua</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnExpandAll">
                            <i class="fas fa-angle-double-down"></i> Expand semua
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnCollapseAll">
                            <i class="fas fa-angle-double-up"></i> Collapse semua
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="btnDeleteSelected" disabled>
                            <i class="fas fa-trash"></i> Hapus terpilih (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                </div>


                <div class="row g-3" id="detailContainer"></div>

                <!-- Card untuk menampilkan detail -->
                <!-- <div class="row mt-4" id="detailContainer">
                </div> -->

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
    (function() {
        const tglPoEl = document.getElementById('tgl_po');
        const container = document.getElementById('detailContainer');
        const selectAllEl = document.getElementById('selectAll');
        const btnDeleteSelected = document.getElementById('btnDeleteSelected');
        const selectedCountEl = document.getElementById('selectedCount');
        const btnExpandAll = document.getElementById('btnExpandAll');
        const btnCollapseAll = document.getElementById('btnCollapseAll');
        const poBooking = document.getElementById('po_booking');
        const searchInput = document.getElementById('searchCard');

        // Fitur pencarian dinamis berdasarkan no_model atau item_type
        searchInput.addEventListener('input', function() {
            const keyword = this.value.trim().toLowerCase();
            const cards = container.querySelectorAll('[data-card]');

            cards.forEach(card => {
                const model = card.querySelector('strong')?.textContent?.toLowerCase() || '';
                const itemType = card.querySelector('small')?.textContent?.toLowerCase() || '';

                if (model.includes(keyword) || itemType.includes(keyword)) {
                    card.style.display = ''; // tampilkan
                } else {
                    card.style.display = 'none'; // sembunyikan
                }
            });
        });


        function toast(msg, icon = 'success') {
            if (!window.Swal) return;
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon,
                title: msg,
                showConfirmButton: false,
                timer: 1500
            });
        }

        function updateToolbarState() {
            const checked = container.querySelectorAll('.select-card:checked').length;
            const total = container.querySelectorAll('.select-card').length;
            selectedCountEl.textContent = checked;
            btnDeleteSelected.disabled = checked === 0;
            selectAllEl.checked = checked > 0 && checked === total;
            selectAllEl.indeterminate = checked > 0 && checked < total;
        }

        function reindexCards() {
            const cols = container.querySelectorAll('[data-card]');
            cols.forEach((col, idx) => {
                col.id = `card-${idx}`;
                col.dataset.index = String(idx);

                // Update name detail[idx]
                col.querySelectorAll('[name^="detail["]').forEach(el => {
                    el.name = el.name.replace(/detail\[\d+\]/, `detail[${idx}]`);
                });

                // Update collapse id/target
                const collapse = col.querySelector('.collapse');
                const toggle = col.querySelector('[data-bs-toggle="collapse"]');
                if (collapse && toggle) {
                    const newId = `collapse-${idx}`;
                    collapse.id = newId;
                    toggle.setAttribute('data-bs-target', `#${newId}`);
                    toggle.setAttribute('aria-controls', newId);
                }

                // Update delete-one index
                const delBtn = col.querySelector('[data-action="delete-one"]');
                if (delBtn) delBtn.dataset.index = String(idx);
            });
        }

        function buildCard(item, index) {
            const collapseId = `collapse-${index}`;
            return `
      <div class="col-md-6" id="card-${index}" data-card data-index="${index}">
        <div class="card border-dark h-100">
          <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3 flex-grow-1">
              <input class="form-check-input select-card" type="checkbox" aria-label="Pilih card">
              <!-- tombol toggle collapse -->
              <button type="button"
                      class="btn btn-sm btn-dark d-flex align-items-center gap-2 flex-grow-1 text-start toggle-collapse"
                      data-bs-toggle="collapse"
                      data-bs-target="#${collapseId}"
                      aria-expanded="false"
                      aria-controls="${collapseId}">
                <span class="d-flex flex-column lh-sm">
                  <strong>${item.no_model ?? ''}</strong>
                  <small class="text-white-50">${item.item_type ?? ''}</small>
                </span>
                <i class="fas fa-chevron-down ms-auto"></i>
              </button>
            </div>
            <div class="d-flex align-items-center gap-2">
              <button type="button" class="btn btn-sm btn-outline-light" data-action="delete-one" data-index="${index}" title="Hapus card ini">
                <i class="fas fa-trash"></i>
              </button>
              <input type="hidden" name="detail[${index}][id_induk]" value="${item.id_induk ?? ''}">
            </div>
          </div>

          <!-- COLLAPSE BODY: default tertutup (class 'collapse' saja) -->
          <div id="${collapseId}" class="collapse">
            <div class="card-body">
              <div class="form-group mb-2">
                <label class="form-label">Item Type <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="detail[${index}][item_type]" value="${item.item_type ?? ''}" required>
                <small class="text-danger">*Sesuaikan Item Type</small>
              </div>
              <div class="form-group mb-2">
                <label class="form-label">Kode Warna</label>
                <input type="text" class="form-control" name="detail[${index}][kode_warna]" value="${item.kode_warna ?? ''}" required>
              </div>
              <div class="form-group mb-2">
                <label class="form-label">Warna</label>
                <input type="text" class="form-control" name="detail[${index}][color]" value="${item.color ?? ''}" required>
              </div>
              <div class="form-group mb-2">
                <label class="form-label">Total KG PO</label>
                <input type="number" step="0.01" class="form-control" name="detail[${index}][kg_total_po]" value="${item.total_kg_po ?? 0}" required readonly>
              </div>
              <div class="form-group mb-0">
                <label class="form-label">KG Celup</label>
                <input type="number" step="0.01" class="form-control" name="detail[${index}][kg_po]" required>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
        }

        // === Stop toggle saat klik checkbox / delete ===
        container.addEventListener('click', (e) => {
            if (e.target.closest('.select-card') || e.target.closest('[data-action="delete-one"]')) {
                e.stopPropagation();
            }
        });

        // Toggle pilih per-card
        container.addEventListener('change', (e) => {
            if (!e.target.classList.contains('select-card')) return;
            const card = e.target.closest('.card');
            if (!card) return;
            card.classList.toggle('is-selected', e.target.checked);
            updateToolbarState();
        });

        // Hapus satu card cepat
        container.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-action="delete-one"]');
            if (!btn) return;
            const col = btn.closest('[data-card]');
            if (!col) return;
            col.classList.add('fade-out');
            setTimeout(() => {
                col.remove();
                reindexCards();
                updateToolbarState();
                toast('Card dihapus');
            }, 220);
        });

        // Pilih semua
        selectAllEl.addEventListener('change', () => {
            const check = selectAllEl.checked;
            container.querySelectorAll('.select-card').forEach(cb => {
                cb.checked = check;
                const c = cb.closest('.card');
                if (c) c.classList.toggle('is-selected', check);
            });
            updateToolbarState();
        });

        // Hapus terpilih
        btnDeleteSelected.addEventListener('click', () => {
            const selectedCols = [...container.querySelectorAll('.select-card:checked')].map(cb => cb.closest('[data-card]'));
            if (selectedCols.length === 0) return;

            if (window.Swal) {
                Swal.fire({
                    title: `Hapus ${selectedCols.length} item?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then(res => {
                    if (!res.isConfirmed) return;
                    selectedCols.forEach(col => col.classList.add('fade-out'));
                    setTimeout(() => {
                        selectedCols.forEach(col => col.remove());
                        reindexCards();
                        updateToolbarState();
                        toast('Item terpilih dihapus');
                    }, 220);
                });
            } else {
                selectedCols.forEach(col => col.remove());
                reindexCards();
                updateToolbarState();
            }
        });

        // Expand/Collapse semua
        function toggleAll(show) {
            const els = container.querySelectorAll('.collapse');
            els.forEach(el => {
                if (window.bootstrap?.Collapse) {
                    const inst = bootstrap.Collapse.getOrCreateInstance(el, {
                        toggle: false
                    });
                    show ? inst.show() : inst.hide();
                } else {
                    // fallback tanpa bootstrap js
                    el.classList.toggle('show', !!show);
                }
            });
        }
        btnExpandAll.addEventListener('click', () => toggleAll(true));
        btnCollapseAll.addEventListener('click', () => toggleAll(false));

        function loadPoData() {
            const tglPo = tglPoEl.value;
            if (!tglPo) return;

            const isBooking = poBooking.checked ? 1 : 0;

            fetch("<?= base_url($role . '/po/getDetailByTglPO') ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'tgl_po=' + encodeURIComponent(tglPo) +
                        '&po_booking=' + encodeURIComponent(isBooking)
                })
                .then(r => r.json())
                .then(data => {
                    container.innerHTML = '';
                    selectAllEl.checked = false;
                    selectAllEl.indeterminate = false;
                    btnDeleteSelected.disabled = true;
                    selectedCountEl.textContent = '0';

                    if (Array.isArray(data) && data.length) {
                        const frag = document.createDocumentFragment();
                        data.forEach((item, index) => {
                            const wrap = document.createElement('div');
                            wrap.innerHTML = buildCard(item, index);
                            frag.appendChild(wrap.firstElementChild);
                        });
                        container.appendChild(frag);
                    } else {
                        container.innerHTML = `<div class="col-12 text-center text-muted">Tidak ada detail PO untuk tanggal ini.</div>`;
                    }
                })
                .catch(() => {
                    container.innerHTML = `<div class="col-12 text-center text-danger">Gagal memuat data.</div>`;
                });
        }
        // trigger fetch saat pilih tanggal
        tglPoEl.addEventListener('change', loadPoData);

        // trigger fetch saat toggle booking (tapi hanya kalau tanggal sudah dipilih)
        poBooking.addEventListener('change', loadPoData);

        // Fetch & render by tanggal (card default collapse)
        // tglPoEl.addEventListener('change', function() {
        //     const tglPo = this.value;
        //     const isBooking = poBooking.checked ? 1 : 0;

        //     fetch("<?= base_url($role . '/po/getDetailByTglPO') ?>", {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/x-www-form-urlencoded',
        //                 'X-Requested-With': 'XMLHttpRequest'
        //             },
        //             body: 'tgl_po=' + encodeURIComponent(tglPo) + '&po_booking=' + encodeURIComponent(isBooking)
        //         })
        //         .then(r => r.json())
        //         .then(data => {
        //             container.innerHTML = '';
        //             selectAllEl.checked = false;
        //             selectAllEl.indeterminate = false;
        //             btnDeleteSelected.disabled = true;
        //             selectedCountEl.textContent = '0';

        //             if (Array.isArray(data) && data.length) {
        //                 const frag = document.createDocumentFragment();
        //                 data.forEach((item, index) => {
        //                     const wrap = document.createElement('div');
        //                     wrap.innerHTML = buildCard(item, index);
        //                     frag.appendChild(wrap.firstElementChild);
        //                 });
        //                 container.appendChild(frag);
        //                 // semua tetap collapsed secara default
        //             } else {
        //                 container.innerHTML = `<div class="col-12 text-center text-muted">Tidak ada detail PO untuk tanggal ini.</div>`;
        //             }
        //         })
        //         .catch(() => {
        //             container.innerHTML = `<div class="col-12 text-center text-danger">Gagal memuat data.</div>`;
        //         });
        // });
    })();
</script>



<?php $this->endSection(); ?>