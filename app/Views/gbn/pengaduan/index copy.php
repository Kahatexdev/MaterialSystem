<?php $this->extend($role . '/pengaduan/header'); ?>
<?= $this->section('content') ?>
<style>
    #filterText:focus,
    #filterUser:focus,
    #filterFrom:focus,
    #filterTo:focus,
    #filterRole:focus {
        box-shadow: 0 0 0 3px rgba(13, 110, 253, .15);
        /* bootstrap-ish ring */
        border-color: #0d6efd;
    }

    /* Card style & soft shadow */
    #modalCreatePengaduan .pengaduan-card {
        border-radius: 1rem;
        border: 0;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
        overflow: hidden;
    }

    /* Header dengan gradien / warna brand */
    /* #modalCreatePengaduan .pengaduan-header {
        border-bottom: 0;
        background: linear-gradient(135deg, #2152ff, #21d4fd);
        color: #fff;
        padding: 1rem 1.25rem;
    }

    #modalCreatePengaduan .pengaduan-header .modal-title {
        font-weight: 600;
    }

    #modalCreatePengaduan .pengaduan-header p {
        opacity: 0.9;
    } */

    /* Body spacing */
    #modalCreatePengaduan .modal-body {
        padding: 1rem 1.5rem 1.25rem;
        background-color: #f8fafc;
    }

    /* Textarea style */
    /* #modalCreatePengaduan .pengaduan-textarea {
        resize: vertical;
        min-height: 120px;
        border-radius: 0.75rem;
    } */

    #modalCreatePengaduan .pengaduan-textarea {
        resize: none;            /* Biar user nggak tarik manual */
        overflow-y: hidden;      /* Jangan pakai scrollbar di dalam textarea */
        min-height: 120px;
        border-radius: 0.75rem;
    }


    /* Small tweak alert */
    /* #modalCreatePengaduan .alert-info {
        border-radius: 0.75rem;
        border: 0;
        background-color: #e0f2ff;
    } */

</style>

<div class="container py-4">

    <!-- Header + Filter -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        <h4 class="mb-0">Daftar Pengaduan</h4>

        <!-- FILTER BAR -->
        <div class="w-100 w-md-auto">
            <div class="row g-2">
                <div class="col-12 col-md-3">
                    <label for="filterText" class="form-label small text-muted">Cari teks (isi / balasan)</label>
                    <input id="filterText" type="text" class="form-control" placeholder="Ketik untuk mencari..." aria-label="Cari teks">
                </div>

                <div class="col-6 col-md-3">
                    <label for="filterRole" class="form-label small text-muted">Role tujuan</label>
                    <select id="filterRole" class="form-select" aria-label="Filter berdasarkan role tujuan">
                        <option value="">Semua Bagian</option>
                        <option value="capacity">Capacity</option>
                        <option value="planning">PPC</option>
                        <option value="aps">Planner</option>
                        <option value="user">Area</option>
                        <option value="rosso">Rosso</option>
                        <option value="gbn">GBN</option>
                        <option value="celup">celup cones</option>
                        <option value="covering">Covering</option>
                        <option value="sudo">Monitoring Planning & Produksi</option>
                        <option value="monitoring">Monitoring Bahan Baku</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label for="filterUser" class="form-label small text-muted">Username pengirim</label>
                    <input id="filterUser" type="text" class="form-control" placeholder="mis. SHABIRA" aria-label="Filter berdasarkan username">
                </div>

                <div class="col-6 col-md-2">
                    <label for="filterFrom" class="form-label small text-muted">Dari tanggal</label>
                    <input id="filterFrom" type="date" class="form-control" aria-label="Tanggal mulai">
                </div>

                <div class="col-6 col-md-2">
                    <label for="filterTo" class="form-label small text-muted">Sampai tanggal</label>
                    <input id="filterTo" type="date" class="form-control" aria-label="Tanggal akhir">
                </div>

                <div class="col-12 d-flex gap-2 mt-1">
                    <button id="btnClearFilter" class="btn btn-light btn-sm" type="button">Reset</button>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreatePengaduan" type="button">
                        + Buat Pengaduan
                    </button>
                </div>
            </div>

            <div class="mt-1 small text-muted">
                <span id="resultCount">0</span> hasil
            </div>
        </div>

    </div>

    <!-- Jika tidak ada pengaduan -->
    <?php if (empty($pengaduan)) : ?>
        <div class="alert alert-info text-center">
            Tidak ada pesan/aduan.
        </div>
    <?php else : ?>

        <!-- LIST CONTAINER (perbaiki: satu id saja) -->
        <div id="listPengaduan" class="d-flex flex-column">

            <?php foreach ($pengaduan as $p) : ?>
                <?php
                $timestamp = strtotime($p['created_at']);
                $formattedDate = '<strong>' . date('l, d/m/Y', $timestamp) . '</strong> (' . date('H:i', $timestamp) . ')';
                $dateISO = date('Y-m-d', $timestamp); // utk filter tanggal

                $roleMap = [
                    'sudo'     => 'monitoring',
                    'aps'      => 'Planner',
                    'planning' => 'PPC',
                    'user'     => 'Area'
                ];
                $displayRole = $roleMap[$p['target_role']] ?? $p['target_role'];

                // kumpulkan teks balasan utk pencarian teks
                $replyTexts = [];
                if (!empty($replies[$p['id_pengaduan']])) {
                    foreach ($replies[$p['id_pengaduan']] as $r) {
                        $replyTexts[] = ($r['username'] ?? '') . ' ' . ($r['isi'] ?? '');
                    }
                }
                $searchBlob = strtolower(
                    ($p['username'] ?? '') . ' ' .
                        ($p['isi'] ?? '') . ' ' .
                        implode(' ', $replyTexts) . ' ' .
                        $displayRole
                );
                ?>
                <div class="card mb-3 pengaduan-card" data-role="<?= esc($p['target_role']) ?>" data-user="<?= esc(strtolower($p['username'])) ?>" data-date="<?= esc($dateISO) ?>" data-search="<?= esc($searchBlob) ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-1">
                                <strong><?= esc($p['username']) ?></strong> →
                                <span class="badge bg-secondary"><?= esc($displayRole) ?></span>
                            </h6>
                            <div class="d-flex align-items-center gap-2">
                                <small class="text-muted"><?= $formattedDate ?></small>
                                <button onclick="window.location.href='<?= base_url('api/pengaduan/exportPdf/' . $p['id_pengaduan']) ?>'" class="btn btn-danger">
                                    <i class="fas fa-file-pdf me-2"></i> PDF
                                </button>
                            </div>
                        </div>

                        <p class="mt-2 mb-2"><?= nl2br(esc($p['isi'])) ?></p>

                        <hr class="my-2">

                        <!-- Reply list -->
                        <?php if (!empty($replies[$p['id_pengaduan']])) : ?>
                            <?php foreach ($replies[$p['id_pengaduan']] as $r) : ?>
                                <div class="border-start ps-2 mb-2">
                                    <strong><?= esc($r['username']) ?></strong>:
                                    <?= nl2br(esc($r['isi'])) ?>
                                    <div><small class="text-muted"><?= esc($r['created_at']) ?></small></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="text-muted small">Belum ada balasan.</div>
                        <?php endif; ?>

                        <!-- Form reply -->
                        <form class="formReply mt-2" data-id="<?= $p['id_pengaduan'] ?>">
                            <div class="input-group">
                                <input type="hidden" name="username" value="<?= session()->get('username') ?>">
                                <textarea name="isi" class="form-control" placeholder="Tulis balasan..." required>
                                </textarea>
                                <button class="btn btn-info" type="submit"><i class="fas fa-paper-plane"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

        </div> <!-- /#listPengaduan -->
    <?php endif; ?>
</div>

<!-- Modal Create Pengaduan -->
<div class="modal fade" id="modalCreatePengaduan" tabindex="-1" aria-labelledby="modalCreatePengaduanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="formCreate" class="modal-content pengaduan-card">
            <div class="modal-header pengaduan-header">
                <div>
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modalCreatePengaduanLabel">
                        <i class="bi bi-chat-dots-fill"></i>
                        Buat Pengaduan Baru
                    </h5>
                    <p class="mb-0 small text-light-50">
                        Sampaikan keluhan atau masukan Anda dengan jelas agar tim kami bisa menindaklanjuti dengan cepat.
                    </p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="username" value="<?= session()->get('username') ?>">

                <!-- Info Tips -->
                <div class="alert alert-info d-flex align-items-start gap-2 small mb-3">
                    <i class="bi bi-info-circle-fill mt-1"></i>
                    <div>
                        <strong>Tips:</strong><br>
                        • Pilih bagian yang sesuai dengan jenis pengaduan Anda.<br>
                        • Jelaskan kronologi singkat, tanggal kejadian, dan detail penting lainnya.
                    </div>
                </div>

                <!-- Target Role -->
                <div class="mb-3">
                    <label for="target_role" class="form-label fw-semibold">
                        Ditujukan ke <span class="text-danger">*</span>
                    </label>
                    <select name="target_role" id="target_role" class="form-select" required>
                        <option value="">— Pilih Bagian —</option>
                        <option value="capacity">Capacity</option>
                        <option value="planning">PPC</option>
                        <option value="aps">Planner</option>
                        <option value="user">Area</option>
                        <option value="rosso">Rosso</option>
                        <option value="gbn">GBN</option>
                        <option value="celup">Celup Cones</option>
                        <option value="covering">Covering</option>
                        <option value="sudo">Monitoring Planning &amp; Produksi</option>
                        <option value="monitoring">Monitoring Bahan Baku</option>
                    </select>
                    <div class="form-text">
                        Pilih tujuan agar aduan langsung diteruskan ke tim yang tepat.
                    </div>
                </div>

                <!-- Isi Pengaduan -->
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="isi" class="form-label fw-semibold mb-1">
                            Isi Pengaduan <span class="text-danger">*</span>
                        </label>
                        <small class="text-muted" id="pengaduanCharCount">0 / 1000</small>
                    </div>
                    <textarea
                        name="isi"
                        id="isi"
                        class="form-control pengaduan-textarea"
                        rows="4"
                        maxlength="1000"
                        placeholder="Contoh: Tanggal xx/xx/2025 terjadi keterlambatan material di area..., sehingga proses produksi terganggu..."
                        required
                    ></textarea>
                    <div class="form-text">
                        Gunakan bahasa yang sopan dan sertakan detail yang jelas.
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-send-fill"></i>
                    Kirim Pengaduan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- AJAX CREATE ---
    $('#formCreate').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: CapacityUrl + 'pengaduan/create',
            method: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Pengaduan terkirim.'
                    });
                    loadPengaduan();
                    $('#modalCreatePengaduan').modal('hide');
                } else {
                    alert('Gagal mengirim: ' + (res.message ?? 'Unknown error'));
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Terjadi kesalahan koneksi ke server.');
            }
        });
    });

    // --- AJAX REPLY ---
    $(document).on('submit', '.formReply', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        $.ajax({
            url: CapacityUrl + 'pengaduan/reply/' + id,
            method: 'POST',
            data: $(this).serialize(),
            success: function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Balasan terkirim.'
                });
                loadPengaduan();
            },
            error: function() {
                alert('Gagal mengirim balasan.');
            }
        });
    });

    // --- RELOAD LIST (tetap pakai #listPengaduan container) ---
    function loadPengaduan() {
        $.get('<?= base_url($role . "/pengaduan") ?>', function(html) {
            const $newList = $(html).find('#listPengaduan').html();
            $('#listPengaduan').html($newList);
            applyFilter(); // re-apply filter setelah reload
        });
    }

    // --- FILTERING LOGIC ---
    const $filterText = $('#filterText');
    const $filterRole = $('#filterRole');
    const $filterUser = $('#filterUser');
    const $filterFrom = $('#filterFrom');
    const $filterTo = $('#filterTo');
    const $resultCount = $('#resultCount');

    function debounce(fn, wait = 200) {
        let t;
        return function() {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, arguments), wait);
        };
    }

    function applyFilter() {
        const q = ($filterText.val() || '').toLowerCase().trim();
        const role = ($filterRole.val() || '').trim();
        const user = ($filterUser.val() || '').toLowerCase().trim();
        const fromStr = $filterFrom.val();
        const toStr = $filterTo.val();

        const from = fromStr ? new Date(fromStr) : null;
        const to = toStr ? new Date(toStr) : null;
        if (to) {
            to.setHours(23, 59, 59, 999);
        } // inklusif

        let visible = 0;
        $('.pengaduan-card').each(function() {
            const $card = $(this);
            const cardRole = ($card.data('role') || '').toString().trim();
            const cardUser = ($card.data('user') || '').toString().trim();
            const cardDateStr = ($card.data('date') || '').toString();
            const blob = ($card.data('search') || '').toString();

            // cek tanggal
            let passDate = true;
            if (from || to) {
                const d = new Date(cardDateStr);
                if (from && d < from) passDate = false;
                if (to && d > to) passDate = false;
            }

            // cek role, user, dan query teks
            const passRole = role === '' || role === cardRole;
            const passUser = user === '' || (cardUser.includes(user));
            const passText = q === '' || blob.includes(q);

            const show = passRole && passUser && passText && passDate;
            $card.toggle(show);
            if (show) visible++;
        });

        $resultCount.text(visible);
    }

    const applyFilterDebounced = debounce(applyFilter, 150);

    $filterText.on('input', applyFilterDebounced);
    $filterRole.on('change', applyFilter);
    $filterUser.on('input', applyFilterDebounced);
    $filterFrom.on('change', applyFilter);
    $filterTo.on('change', applyFilter);
    $('#btnClearFilter').on('click', function() {
        $filterText.val('');
        $filterRole.val('');
        $filterUser.val('');
        $filterFrom.val('');
        $filterTo.val('');
        applyFilter();
    });

    // inisialisasi awal
    $(function() {
        applyFilter();
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const textarea      = document.getElementById('isi');
    const counter       = document.getElementById('pengaduanCharCount');
    const tags          = document.querySelectorAll('.pengaduan-tag');
    const kategoriInput = document.getElementById('kategori');

    function autoResize(el) {
        el.style.height = 'auto';           // reset dulu
        el.style.height = el.scrollHeight + 'px'; // sesuaikan tinggi dengan isi
    }

    if (textarea) {
        // Saat pertama kali load (kalau ada value)
        autoResize(textarea);

        textarea.addEventListener('input', function () {
            autoResize(this);

            // Update counter kalau ada
            if (counter) {
                const length = this.value.length;
                counter.textContent = `${length} / ${this.maxLength}`;
            }
        });
    }

    // Optional: kalau modal pakai Bootstrap 5, refresh tinggi saat modal dibuka
    const modalEl = document.getElementById('modalCreatePengaduan');
    if (modalEl) {
        modalEl.addEventListener('shown.bs.modal', function () {
            if (textarea) autoResize(textarea);
        });
    }
});
</script>


<?= $this->endSection() ?>