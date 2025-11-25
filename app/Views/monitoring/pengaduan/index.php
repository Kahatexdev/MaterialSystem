<?php $this->extend($role . '/pengaduan/header'); ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    /* ====== THEME ====== */
    :root {
        --brand: #4f46e5;
        /* indigo-600 */
        --brand-2: #22c55e;
        /* green-500 */
        --text-muted: #6b7280;
        --card-shadow: 0 10px 25px rgba(2, 6, 23, .06);
        --ring: 0 0 0 3px rgba(79, 70, 229, .15);
    }

    .bg-gradient-brand {
        background: linear-gradient(135deg, rgba(79, 70, 229, .95), rgba(34, 197, 94, .9));
    }

    .btn-brand {
        background: var(--brand);
        border: none;
    }

    .btn-brand:hover {
        filter: brightness(.95);
    }

    /* ====== HEADER ====== */
    .page-head {
        border-radius: 18px;
        color: #fff;
        box-shadow: var(--card-shadow);
    }

    /* ====== LIST ====== */
    #pengaduanList .complaint-card {
        border: 0;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        transition: transform .15s ease, box-shadow .15s ease;
    }

    #pengaduanList .complaint-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 40px rgba(2, 6, 23, .08);
    }

    .timeline-accent {
        border-left: 4px solid rgba(79, 70, 229, .25);
        padding-left: .9rem;
    }

    .role-badge {
        font-weight: 600;
        text-transform: capitalize;
        letter-spacing: .2px;
    }

    /* warna badge per role */
    .role-capacity {
        background: #0ea5e9 !important;
    }

    .role-planning {
        background: #f59e0b !important;
    }

    .role-aps {
        background: #6366f1 !important;
    }

    .role-user {
        background: #10b981 !important;
    }

    .role-rosso {
        background: #ef4444 !important;
    }

    .role-gbn {
        background: #a855f7 !important;
    }

    .role-sudo {
        background: #334155 !important;
    }

    .role-monitoring {
        background: #14b8a6 !important;
    }

    .meta-time {
        color: var(--text-muted);
        font-size: .875rem;
    }

    /* ====== REPLIES ====== */
    .reply-item {
        border-left: 3px solid rgba(34, 197, 94, .35);
        margin-left: .25rem;
        padding-left: .75rem;
    }

    .reply-input {
        border-radius: 999px;
    }

    .reply-input:focus {
        box-shadow: var(--ring);
        border-color: var(--brand);
    }

    /* ====== EMPTY STATE ====== */
    .empty-state {
        border: 0;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
    }

    .reply-textarea {
        border-radius: 12px;
        resize: none;
        /* user tidak bisa drag */
        min-height: 38px;
        max-height: 150px;
        /* biar ga kebablasan */
        overflow-y: hidden;
        /* sembunyikan scrollbar */
        line-height: 1.4;
    }

    /* filter inputs focus ring */
    #filterText:focus,
    #filterUser:focus,
    #filterFrom:focus,
    #filterTo:focus,
    #filterRole:focus {
        box-shadow: var(--ring);
        border-color: var(--brand);
    }



    .reply-textarea:focus {
        box-shadow: var(--ring);
        border-color: var(--brand);
    }

    .card.border-warning {
        box-shadow: 0 0 0 2px rgba(245, 158, 11, .35);
    }

    .quick-reply-chip {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 999px;
        background: #f3f4f6;
        font-size: .75rem;
        cursor: pointer;
        border: 1px solid transparent;
        margin-top: 4px;
        margin-right: 4px;
        color: #374151;
    }

    .quick-reply-chip:hover {
        background: #e5e7eb;
        border-color: rgba(79, 70, 229, .35);
    }

    .quick-reply-label {
        font-size: .75rem;
        color: var(--text-muted);
        margin-top: 4px;
    }


    /* ====== OVERLAY ====== */
    #loadingOverlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .3);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(2px);
    }

    .spinner {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 4px solid #fff;
        border-top-color: transparent;
        animation: spin .8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<div class="container py-4">

    <!-- HEADER -->
    <div class="page-head bg-gradient-brand p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-chat-left-text fs-2"></i>
            <div>
                <h4 class="mb-0 fw-bold">Daftar Pengaduan</h4>
                <div class="opacity-75">Kelola pesan & balasan lintas bagian</div>
            </div>
        </div>
        <button class="btn btn-light mt-3 mt-md-0" data-bs-toggle="modal" data-bs-target="#modalCreatePengaduan">
            <i class="bi bi-plus-lg me-1"></i> Buat Pengaduan
        </button>
    </div>
    <!-- FILTER BAR -->
    <div class="card mb-3" style="border:0; border-radius:16px; box-shadow: var(--card-shadow);">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small text-muted">Cari teks (isi / balasan)</label>
                    <input id="filterText" type="text" class="form-control" placeholder="Ketik untuk mencari...">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Role tujuan</label>
                    <select id="filterRole" class="form-select">
                        <option value="">Semua</option>
                        <option value="capacity">Capacity</option>
                        <option value="planning">PPC</option>
                        <option value="aps">Planner</option>
                        <option value="user">Area</option>
                        <option value="rosso">Rosso</option>
                        <option value="gbn">GBN</option>
                        <option value="sudo">Monitoring Planning & Produksi</option>
                        <option value="monitoring">Monitoring Bahan Baku</option>
                        <option value="covering">Covering</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Username pengirim</label>
                    <input id="filterUser" type="text" class="form-control" placeholder="mis. SHABIRA">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Dari tanggal</label>
                    <input id="filterFrom" type="date" class="form-control">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted">Sampai tanggal</label>
                    <input id="filterTo" type="date" class="form-control">
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="filterUnread">
                        <label class="form-check-label small" for="filterUnread">Belum dibaca</label>
                    </div>
                </div>


            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="small text-muted"><span id="resultCount">0</span> hasil</div>
                <div class="d-flex gap-1">
                    <button id="btnClearFilter" class="btn btn-light btn-sm">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- EMPTY / LIST -->
    <?php if (empty($pengaduan)) : ?>
        <div class="alert empty-state bg-white text-center p-5">
            <div class="mb-2" style="font-size:42px">🗒️</div>
            <h5 class="mb-1">Belum ada pengaduan</h5>
            <div class="text-muted mb-3">Klik “Buat Pengaduan” untuk mengirim pesan pertama Anda.</div>
            <button class="btn btn-brand text-white" data-bs-toggle="modal" data-bs-target="#modalCreatePengaduan">
                Mulai Buat Pengaduan
            </button>
        </div>
    <?php else : ?>
        <!-- gunakan satu container ID untuk di-reload -->
        <div id="pengaduanList">
            <?php
            // daftar balasan cepat default
            $quickReplies = [
                'Data sudah diperbaiki, coba dicek lagi.',
                'Baik sebentar, Datanya kami dicek dulu.',
                'Silahkan Konfirmasi Ke Gudang Benang.',
                'Sudah dikirim permintaan manualnya, silahkan dicek lagi.',
            ];
            ?>

            <?php foreach ($pengaduan as $p) : ?>
                <?php
                $timestamp = strtotime($p['created_at']);
                $formattedDate = '<strong>' . date('l, d/m/Y', $timestamp) . '</strong> (' . date('H:i', $timestamp) . ')';
                $dateISO = date('Y-m-d', $timestamp);

                $roleMap = [
                    'sudo'       => 'Monitoring Planning & Produksi',
                    'aps'        => 'Planner',
                    'planning'   => 'PPC',
                    'user'       => 'Area',
                    'capacity'   => 'Capacity',
                    'rosso'      => 'Rosso',
                    'gbn'        => 'GBN',
                    'celup'      => 'Celup Cones',
                    'monitoring' => 'Monitoring Bahan Baku',
                ];
                $displayRole = $roleMap[$p['target_role']] ?? $p['target_role'];
                $roleClass = 'role-' . preg_replace('/[^a-z0-9]/', '', strtolower($p['target_role']));

                // kumpulkan teks balasan (untuk filter text)
                $replyTexts = [];
                $hasReply = 0;
                if (!empty($replies[$p['id_pengaduan']])) {
                    foreach ($replies[$p['id_pengaduan']] as $r) {
                        $replyTexts[] = ($r['username'] ?? '') . ' ' . ($r['isi'] ?? '');
                    }
                    $hasReply = 1;
                }
                $searchBlob = strtolower(
                    ($p['username'] ?? '') . ' ' .
                        ($p['isi'] ?? '') . ' ' .
                        implode(' ', $replyTexts) . ' ' .
                        $displayRole
                );
                ?>
                <?php
                // Unread = tidak ada balasan
                $isUnread = $hasReply ? 0 : 1;
                ?>

                <div class="card complaint-card mb-3 <?= $isUnread ? 'border-warning' : '' ?>" data-complaint-id="<?= $p['id_pengaduan'] ?>" data-role="<?= esc($p['target_role']) ?>" data-user="<?= esc(strtolower($p['username'])) ?>" data-date="<?= esc($dateISO) ?>" data-hasreply="<?= $hasReply ?>" data-unread="<?= $isUnread ?>" data-search="<?= esc($searchBlob) ?>">

                    <div class="card-body timeline-accent">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <h6 class="mb-1">
                                    <?php if ($isUnread) : ?>
                                        <span class="badge bg-warning text-dark me-2">Baru</span>
                                    <?php endif; ?>
                                    <span class="fw-bold"><?= esc($p['username']) ?></span>
                                    <i class="bi bi-arrow-right-short mx-1 opacity-50"></i>
                                    <span class="badge role-badge <?= $roleClass ?>"><?= esc($displayRole) ?></span>
                                </h6>

                                <div class="meta-time"><?= $formattedDate ?></div>
                            </div>
                            <div class="dropstart">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                <ul class="dropdown-menu">
                                    <li><button class="dropdown-item btn-refresh-one" data-id="<?= $p['id_pengaduan'] ?>"><i class="bi bi-arrow-clockwise me-1"></i>Refresh item</button></li>
                                </ul>
                            </div>
                        </div>

                        <p class="mt-3 mb-2"><?= nl2br(esc($p['isi'])) ?></p>
                        <hr class="my-3">

                        <?php if (!empty($replies[$p['id_pengaduan']])) : ?>
                            <?php foreach ($replies[$p['id_pengaduan']] as $r) : ?>
                                <div class="reply-item mb-2">
                                    <strong><?= esc($r['username']) ?></strong>:
                                    <?= nl2br(esc($r['isi'])) ?>
                                    <div><small class="text-muted"><?= esc($r['created_at']) ?></small></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="text-muted small">Belum ada balasan.</div>
                        <?php endif; ?>

                        <form class="formReply mt-3" data-id="<?= $p['id_pengaduan'] ?>">
                            <div class="d-flex gap-2 align-items-start">
                                <textarea name="isi" class="form-control reply-textarea auto-resize" rows="1" placeholder="Tulis balasan..." minlength="2" required></textarea>
                                <button class="btn btn-brand text-white px-3" type="submit"><i class="bi bi-send-fill"></i></button>
                            </div>

                            <!-- Balas Cepat -->
                            <div class="quick-reply-label">Balas cepat:</div>
                            <div class="mt-1">
                                <?php foreach ($quickReplies as $qr) : ?>
                                    <span class="quick-reply-chip" data-text="<?= esc($qr) ?>">
                                        <?= esc($qr) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="username" value="<?= esc(session()->get('username')) ?>">
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- LOADING OVERLAY -->
<div id="loadingOverlay">
    <div class="spinner"></div>
</div>

<!-- Modal Create Pengaduan -->
<div class="modal fade" id="modalCreatePengaduan" tabindex="-1" aria-labelledby="modalCreatePengaduanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formCreate" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreatePengaduanLabel"><i class="bi bi-plus-circle me-2"></i>Buat Pengaduan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="username" value="<?= esc(session()->get('username')) ?>">

                <div class="mb-3">
                    <label for="target_role" class="form-label">Ditujukan ke</label>
                    <select name="target_role" id="target_role" class="form-select" required>
                        <option value="">-- Pilih Bagian --</option>
                        <option value="capacity">Capacity</option>
                        <option value="planning">PPC</option>
                        <option value="aps">Planner</option>
                        <option value="user">Area</option>
                        <option value="rosso">Rosso</option>
                        <option value="gbn">GBN</option>
                        <option value="celup">Celup Cones</option>
                        <option value="sudo">Monitoring Planning & Produksi</option>
                        <option value="monitoring">Monitoring Bahan Baku</option>
                        <option value="covering">Covering</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label for="isi" class="form-label">Isi Pengaduan</label>
                    <textarea name="isi" id="isi" class="form-control" rows="3" placeholder="Tulis aduan Anda..." minlength="5" required></textarea>
                </div>
                <div class="form-text">Tulis konteks yang jelas agar tim terkait mudah menindaklanjuti.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-brand text-white">Kirim</button>
            </div>
        </form>
    </div>
</div>

<script>
    // UTIL: overlay
    function showLoading(on) {
        $('#loadingOverlay').css('display', on ? 'flex' : 'none');
    }

    // AUTO RESIZE TEXTAREA
    $(document).on('input', '.auto-resize', function() {
        this.style.height = 'auto'; // reset dulu
        this.style.height = (this.scrollHeight) + 'px'; // sesuaikan isi
    });


    // CREATE
    $('#formCreate').on('submit', function(e) {
        e.preventDefault();
        showLoading(true);
        $.ajax({
            url: 'http://172.23.44.14/CapacityApps/public/api/pengaduan/create',
            method: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(res) {
                showLoading(false);
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terkirim',
                        text: res.message || 'Pengaduan berhasil dibuat.'
                    });
                    $('#modalCreatePengaduan').modal('hide');
                    $('#formCreate')[0].reset();
                    loadPengaduan();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal',
                        text: res.message || 'Gagal mengirim pengaduan.'
                    });
                }
            },
            error: function(xhr) {
                showLoading(false);
                const msg = xhr.responseJSON?.message || 'Terjadi kesalahan koneksi ke server.';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg
                });
            }
        });
    });

    // REPLY
    $(document).on('submit', '.formReply', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const $form = $(this);
        showLoading(true);
        $.ajax({
            url: 'http://172.23.44.14/CapacityApps/public/api/pengaduan/reply/' + id,
            method: 'POST',
            data: $form.serialize(),
            success: function(res) {
                showLoading(false);
                Swal.fire({
                    icon: 'success',
                    title: 'Balasan terkirim',
                    text: res.message || 'Balasan telah ditambahkan.'
                });
                $form[0].reset();
                // refresh hanya 1 item atau seluruh list (di sini seluruh list untuk simpel)
                loadPengaduan();
            },
            error: function(xhr) {
                showLoading(false);
                const msg = xhr.responseJSON?.message || 'Gagal mengirim balasan.';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg
                });
            }
        });
    });

    // Refresh satu kartu (opsional)
    $(document).on('click', '.btn-refresh-one', function() {
        const id = $(this).data('id');
        // untuk implementasi granular, sediakan endpoint load satu pengaduan.
        loadPengaduan(); // sementara refresh all
    });


    // element filter
    const $filterText = $('#filterText');
    const $filterRole = $('#filterRole');
    const $filterUser = $('#filterUser');
    const $filterFrom = $('#filterFrom');
    const $filterTo = $('#filterTo');
    // const $filterHasReply = $('#filterHasReply');
    const $resultCount = $('#resultCount');
    const $filterUnread = $('#filterUnread');


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
        // const needReply = $filterHasReply.is(':checked');
        const needUnread = $filterUnread.is(':checked');

        const from = fromStr ? new Date(fromStr) : null;
        const to = toStr ? new Date(toStr) : null;
        if (to) {
            to.setHours(23, 59, 59, 999);
        }
        let visible = 0;
        $('#pengaduanList .complaint-card').each(function() {
            const $card = $(this);
            const cardRole = ($card.data('role') || '').toString().trim();
            const cardUser = ($card.data('user') || '').toString().trim();
            const cardDateStr = ($card.data('date') || '').toString();
            const blob = ($card.data('search') || '').toString();
            const unread = Number($card.data('unread')) === 1; // <-- baca atribut baru

            // tanggal
            let passDate = true;
            if (from || to) {
                const d = new Date(cardDateStr);
                if (from && d < from) passDate = false;
                if (to && d > to) passDate = false;
            }

            const passRole = role === '' || role === cardRole;
            const passUser = user === '' || cardUser.includes(user);
            const passText = q === '' || blob.includes(q);
            const passUnread = !needUnread || unread; // <-- filter unread

            const show = passRole && passUser && passText && passDate && passUnread;
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
    $filterUnread.on('change click', applyFilter);
    // $filterHasReply.on('change', applyFilter);
    $('#btnClearFilter').on('click', function() {
        $filterText.val('');
        $filterRole.val('');
        $filterUser.val('');
        $filterFrom.val('');
        $filterTo.val('');
        // $filterHasReply.prop('checked', false);
        $filterUnread.prop('checked', false); // reset
        applyFilter();
    });

    // re-apply setelah reload list
    function afterListReload() {
        applyFilter();
    }

    // override fungsi loadPengaduan agar panggil applyFilter()
    function loadPengaduan() {
        $.get('<?= base_url($role . "/pengaduan") ?>', function(html) {
            const $newList = $(html).find('#pengaduanList').html();
            if ($newList) {
                $('#pengaduanList').html($newList);
                afterListReload();
            }
        });
    }

    // init
    $(function() {
        applyFilter();
    });

    // QUICK REPLY: klik chip -> isi textarea
    $(document).on('click', '.quick-reply-chip', function() {
        const text = $(this).data('text') || '';
        const $form = $(this).closest('.formReply');
        const $ta = $form.find('.reply-textarea');

        const current = ($ta.val() || '').trim();

        // kalau sudah ada teks, tambahkan di belakang, kalau belum, langsung isi
        const newVal = current ?
            current + (current.endsWith('.') ? ' ' : ' - ') + text :
            text;

        $ta.val(newVal + ' ');
        $ta.trigger('input').focus(); // trigger auto-resize & fokus ke textarea
    });
</script>


<?= $this->endSection() ?>