<?php $this->extend($role . '/pengaduan/header'); ?>
<?= $this->section('content') ?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --primary: #0d6efd;
        --primary-light: #e7f1ff;
        --primary-dark: #0b5ed7;
        --success: #198754;
        --danger: #dc3545;
        --warning: #ffc107;
        --info: #0dcaf0;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-600: #475569;
        --gray-700: #334155;
        --radius: 12px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background: linear-gradient(135deg, #f8fafc 0%, #e8f1ff 100%);
        /* font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; */
        min-height: 100vh;
        color: var(--gray-700);
    }

    .container {
        max-width: 1200px;
    }

    /* ===== HEADER SECTION ===== */
    .header-section {
        background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
        color: white;
        padding: 2.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 24px 24px;
        box-shadow: 0 10px 40px rgba(13, 110, 253, 0.15);
        position: relative;
        overflow: hidden;
    }

    .header-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .header-content {
        position: relative;
        z-index: 1;
    }

    .header-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-subtitle {
        opacity: 0.9;
        font-size: 0.95rem;
    }

    /* ===== FILTER SECTION ===== */
    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: var(--radius);
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border-left: 4px solid var(--primary);
        transition: var(--transition);
    }

    .filter-section:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .filter-title {
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .filter-title i {
        color: var(--primary);
    }

    .form-label {
        font-weight: 500;
        font-size: 0.85rem;
        color: var(--gray-600);
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control,
    .form-select {
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: var(--transition);
        background-color: var(--gray-50);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary);
        background-color: white;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.5rem 1.25rem;
        transition: var(--transition);
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
    }

    .btn-light {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 1.5px solid var(--gray-200);
    }

    .btn-light:hover {
        background: var(--gray-200);
        border-color: var(--gray-300);
    }

    .result-count {
        font-size: 0.9rem;
        color: var(--gray-600);
        margin-top: 0.75rem;
        font-weight: 500;
    }

    /* ===== CARD PENGADUAN ===== */

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .pengaduan-card:hover {
        border-color: var(--primary);
        box-shadow: 0 8px 30px rgba(13, 110, 253, 0.15);
        transform: translateY(-4px);
    }

    .pengaduan-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* ===== REPLY SECTION ===== */

    /* ===== FORM REPLY ===== */
    .form-reply {
        display: flex;
        gap: 0.75rem;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px dashed rgba(148, 163, 184, 0.45);
    }

    .form-reply textarea {
        border: 1.5px solid var(--gray-200);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-family: inherit;
        font-size: 0.95rem;
        resize: none;
        min-height: 80px;
        max-height: 200px;
        overflow-y: auto;
        transition: var(--transition);
        flex: 1;
        background: rgba(248, 250, 252, 0.9);
        border-color: rgba(148, 163, 184, 0.7);
        font-size: 0.9rem;
    }

    .form-reply textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        outline: none;
    }

    .form-reply button {
        padding: 0.75rem 1.25rem;
        background: linear-gradient(135deg, var(--info) 0%, #0dbcf0 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        align-self: flex-end;
        box-shadow: 0 4px 12px rgba(13, 202, 240, 0.3);
    }

    .form-reply button i {
        font-size: 1rem;
    }

    .form-reply button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(13, 202, 240, 0.4);
    }

    /* ===== MODAL ===== */
    .modal-content {
        border: none;
        border-radius: var(--radius);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--info) 100%);
        color: white;
        border: none;
        border-radius: var(--radius) var(--radius) 0 0;
        padding: 1.75rem;
    }

    .modal-title {
        font-weight: 700;
        font-size: 1.25rem;
    }

    .modal-body {
        padding: 1.75rem;
    }

    .modal-footer {
        border-top: 1.5px solid var(--gray-200);
        padding: 1.25rem;
        gap: 1rem;
    }

    .alert-info {
        background: linear-gradient(135deg, #e7f1ff 0%, #f0f7ff 100%);
        border: 1.5px solid rgba(13, 110, 253, 0.2);
        color: var(--primary);
        border-radius: 8px;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        background: white;
        border-radius: var(--radius);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .empty-state i {
        font-size: 3.5rem;
        color: var(--gray-300);
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: var(--gray-600);
        font-size: 1.05rem;
    }

    /* TEXTAREA AUTO RESIZE + SCROLL LIMIT */
    textarea.auto-resize {
        resize: none;
        overflow-y: auto;
        min-height: 80px;
        max-height: 220px;
        /* batas tinggi */
        transition: height .2s ease;
    }

    /* ===== CARD PENGADUAN (UTAMA) ===== */
    .pengaduan-card {
        position: relative;
        background: linear-gradient(145deg, #ffffff 0%, #f9fbff 100%);
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.25);
        padding: 1.5rem 1.5rem 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow:
            0 18px 45px rgba(15, 23, 42, 0.12),
            0 0 0 1px rgba(148, 163, 184, 0.15);
        transition: var(--transition);
        overflow: hidden;
    }

    .pengaduan-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top left,
                rgba(56, 189, 248, 0.15),
                transparent 60%);
        opacity: 0;
        pointer-events: none;
        transition: var(--transition);
    }

    .pengaduan-card:hover {
        transform: translateY(-4px);
        box-shadow:
            0 22px 60px rgba(15, 23, 42, 0.16),
            0 0 0 1px rgba(59, 130, 246, 0.2);
    }

    .pengaduan-card:hover::before {
        opacity: 1;
    }

    /* header atas: avatar + nama + badge + waktu + tombol pdf */
    .pengaduan-header {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding-bottom: 0.75rem;
        margin-bottom: 0.75rem;
        border-bottom: 1px dashed rgba(148, 163, 184, 0.5);
    }

    .pengaduan-sender {
        display: flex;
        align-items: center;
        gap: 0.9rem;
    }

    .sender-avatar {
        width: 44px;
        height: 44px;
        border-radius: 999px;
        background: conic-gradient(from 210deg, #0ea5e9, #6366f1, #0ea5e9);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
    }

    .sender-info h6 {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 600;
        color: var(--gray-700);
        font-size: 1rem;
    }

    .sender-info .badge {
        background: rgba(15, 23, 42, 0.9);
        padding: 0.25rem 0.6rem;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-radius: 999px;
        color: #e5e7eb;
    }

    /* meta kanan: waktu + PDF button */
    .pengaduan-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .pengaduan-time {
        font-size: 0.8rem;
        color: var(--gray-600);
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.03);
    }

    .pengaduan-time i {
        font-size: 0.9rem;
        color: #0ea5e9;
    }

    /* tombol pdf */
    .btn-pdf {
        background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        color: white;
        padding: 0.35rem 0.85rem;
        font-size: 0.8rem;
        border-radius: 999px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        box-shadow: 0 10px 22px rgba(239, 68, 68, 0.3);
        transition: var(--transition);
    }

    .btn-pdf i {
        font-size: 0.85rem;
    }

    .btn-pdf:hover {
        transform: translateY(-1px) scale(1.02);
        box-shadow: 0 14px 30px rgba(239, 68, 68, 0.4);
    }

    /* isi aduan (body utama) */
    .pengaduan-content {
        position: relative;
        background: rgba(248, 250, 252, 0.95);
        padding: 1.1rem 1.1rem 1rem;
        border-radius: 14px;
        margin: 0.75rem 0 0.9rem;
        border: 1px solid rgba(148, 163, 184, 0.35);
        line-height: 1.65;
        color: var(--gray-700);
        font-size: 0.92rem;
    }

    .pengaduan-content::before {
        content: 'Aduan';
        position: absolute;
        top: -0.9rem;
        left: 1rem;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        background: #0f172a;
        color: #e5e7eb;
        padding: 0.1rem 0.55rem;
        border-radius: 999px;
    }

    .pengaduan-divider {
        margin: 0.9rem 0 0.8rem;
        border: 0;
        border-top: 1px dashed rgba(148, 163, 184, 0.6);
    }

    /* ===== REPLY SECTION (BALASAN) ===== */
    /* Container list balasan */
    .replies-container {
        margin: 0.25rem 0 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    /* Row untuk atur kiri-kanan */
    .reply-row {
        display: flex;
        width: 100%;
    }

    /* balasan orang lain (kiri) */
    .reply-row-other {
        justify-content: flex-start;
    }

    /* balasan saya (kanan) */
    .reply-row-me {
        justify-content: flex-end;
    }

    /* Bubble dasar */
    .reply-item {
        max-width: 80%;
        position: relative;
        padding: 0.6rem 0.9rem 0.5rem;
        border-radius: 14px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.18);
        border: 1px solid transparent;
    }

    /* bubble orang lain (kiri) */
    .reply-other {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-color: rgba(148, 163, 184, 0.6);
        border-bottom-left-radius: 4px;
        /* sudut kiri bawah lebih tajam (kayak ekor) */
    }

    /* bubble saya (kanan) */
    .reply-me {
        background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);
        border-color: rgba(37, 99, 235, 0.8);
        color: #f9fafb;
        border-bottom-right-radius: 4px;
        /* sudut kanan bawah lebih tajam */
    }

    /* teks di bubble */
    .reply-me .reply-text {
        color: #eff6ff;
    }

    .reply-other .reply-text {
        color: #374151;
    }

    /* header author kecil */
    .reply-author {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-weight: 600;
        font-size: 0.8rem;
        margin-bottom: 0.1rem;
    }

    .reply-me .reply-author {
        color: #e0f2fe;
    }

    .reply-other .reply-author {
        color: #4f46e5;
    }

    /* icon reply */
    .reply-author i {
        font-size: 0.8rem;
    }

    /* teks isi */
    .reply-text {
        line-height: 1.5;
        font-size: 0.88rem;
        margin-bottom: 0.2rem;
    }

    /* waktu di bawah kanan */
    .reply-time {
        font-size: 0.72rem;
        opacity: 0.8;
        text-align: right;
    }

    /* warna waktu mengikuti bubble */
    .reply-me .reply-time {
        color: #dbeafe;
    }

    .reply-other .reply-time {
        color: #6b7280;
    }

    /* kalau belum ada balasan */
    .no-reply {
        text-align: center;
        padding: 0.9rem 0.75rem;
        color: #9ca3af;
        font-style: italic;
        font-size: 0.85rem;
        border-radius: 10px;
        border: 1px dashed rgba(148, 163, 184, 0.6);
        background: rgba(248, 250, 252, 0.8);
    }




    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .header-title {
            font-size: 1.5rem;
        }

        .pengaduan-header {
            flex-direction: column;
        }

        .filter-section {
            padding: 1rem;
        }

        .form-reply {
            flex-direction: column;
        }

        .form-reply button {
            align-self: stretch;
        }
    }
</style>

<!-- Header -->
<div class="header-section">
    <div class="container">
        <div class="header-content">
            <div class="header-title">
                <i class="fas fa-comments"></i>
                Sistem Pengaduan &amp; Masukan
            </div>
            <div class="header-subtitle">
                Sampaikan keluhan atau masukan Anda dengan jelas agar tim kami bisa menindaklanjuti dengan cepat
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-title">
            <i class="fas fa-sliders-h"></i>
            Filter &amp; Pencarian
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-3">
                <label for="filterText" class="form-label">Cari Teks</label>
                <input id="filterText" type="text" class="form-control" placeholder="Isi atau balasan...">
            </div>

            <div class="col-6 col-md-3">
                <label for="filterRole" class="form-label">Tujuan</label>
                <select id="filterRole" class="form-select">
                    <option value="">Semua Bagian</option>
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
            </div>

            <div class="col-6 col-md-2">
                <label for="filterUser" class="form-label">Pengirim</label>
                <input id="filterUser" type="text" class="form-control" placeholder="Username...">
            </div>

            <div class="col-6 col-md-2">
                <label for="filterFrom" class="form-label">Dari</label>
                <input id="filterFrom" type="date" class="form-control">
            </div>

            <div class="col-6 col-md-2">
                <label for="filterTo" class="form-label">Sampai</label>
                <input id="filterTo" type="date" class="form-control">
            </div>
        </div>

        <div class="filter-actions">
            <button id="btnClearFilter" class="btn btn-light" type="button">
                <i class="fas fa-redo"></i> Reset
            </button>
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalCreatePengaduan">
                <i class="fas fa-plus-circle"></i> Buat Pengaduan
            </button>
        </div>

        <div class="result-count">
            <i class="fas fa-list"></i> <span id="resultCount">0</span> hasil ditemukan
        </div>
    </div>

    <!-- Empty State -->
    <div
        class="empty-state"
        id="emptyState"
        style="<?= empty($pengaduan) ? '' : 'display:none;' ?>">
        <i class="fas fa-inbox"></i>
        <p>Tidak ada pengaduan untuk ditampilkan</p>
    </div>

    <!-- List Pengaduan -->
    <div id="listPengaduan">
        <?php if (!empty($pengaduan)) : ?>

            <?php
            // kalau dari controller namanya $reply, kita map ke $replies
            if (!isset($replies) && isset($reply)) {
                $replies = $reply;
            }
            ?>

            <?php foreach ($pengaduan as $p) : ?>
                <?php
                $timestamp   = strtotime($p['created_at']);
                $formattedTs = date('d M Y', $timestamp) . ' (' . date('H:i', $timestamp) . ')';
                $dateISO     = date('Y-m-d', $timestamp);

                $roleMap = [
                    'sudo'     => 'Monitoring',
                    'aps'      => 'Planner',
                    'planning' => 'PPC',
                    'user'     => 'Area',
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

                $username      = $p['username'] ?? '';
                $avatarInitial = strtoupper(mb_substr($username, 0, 1));
                ?>
                <div
                    class="pengaduan-card"
                    data-id="<?= (int)$p['id_pengaduan'] ?>"
                    data-role="<?= esc($p['target_role']) ?>"
                    data-user="<?= esc(strtolower($username)) ?>"
                    data-date="<?= esc($dateISO) ?>"
                    data-search="<?= esc($searchBlob) ?>">
                    <div class="pengaduan-header">
                        <div class="pengaduan-sender">
                            <div class="sender-avatar"><?= esc($avatarInitial) ?></div>
                            <div class="sender-info">
                                <h6>
                                    <?= esc($username) ?> <i class="fa-solid fa-angles-right"></i>
                                    <span class="badge"><?= esc($displayRole) ?></span>
                                </h6>
                            </div>
                        </div>
                        <div class="pengaduan-meta">
                            <div class="pengaduan-time">
                                <i class="fas fa-clock"></i>
                                <strong><?= esc($formattedTs) ?></strong>
                            </div>
                            <!-- <button
                                type="button"
                                class="btn-pdf"
                                onclick="window.location.href='<?= base_url('api/pengaduan/exportPdf/' . $p['id_pengaduan']) ?>'">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button> -->
                        </div>
                    </div>

                    <div class="pengaduan-content">
                        <?= nl2br(esc($p['isi'])) ?>
                    </div>

                    <hr class="pengaduan-divider">

                    <!-- Replies -->
                    <div class="replies-container">
                        <?php if (!empty($replies[$p['id_pengaduan']])) : ?>
                            <?php foreach ($replies[$p['id_pengaduan']] as $r) : ?>
                                <?php
                                $me    = session()->get('username');
                                $isMe  = strtolower($r['username'] ?? '') === strtolower($me ?? '');
                                ?>
                                <div class="reply-row <?= $isMe ? 'reply-row-me' : 'reply-row-other' ?>">
                                    <div class="reply-item <?= $isMe ? 'reply-me' : 'reply-other' ?>"
                                        data-reply-id="<?= (int)($r['id_reply'] ?? 0) ?>">
                                        <div class="reply-author">
                                            <i class="fas fa-reply"></i>
                                            <?= esc($r['username']) ?>
                                        </div>
                                        <div class="reply-text">
                                            <?= nl2br(esc($r['isi'])) ?>
                                        </div>
                                        <div class="reply-time">
                                            <?= esc($r['created_at']) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="no-reply">
                                Belum ada balasan.
                            </div>
                        <?php endif; ?>
                    </div>


                    <!-- Form reply -->
                    <form class="form-reply" data-id="<?= $p['id_pengaduan'] ?>">
                        <input type="hidden" name="username" value="<?= esc(session()->get('username')) ?>">
                        <textarea name="isi" class="auto-resize" placeholder="Tulis balasan..." required></textarea>
                        <button type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</div>

<!-- Modal Create Pengaduan -->
<div class="modal fade" id="modalCreatePengaduan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!-- FORM membungkus body + footer -->
        <form id="formCreate" class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">
                        <i class="fas fa-pen-fancy"></i> Buat Pengaduan Baru
                    </h5>
                    <small style="opacity: 0.9;">Sampaikan keluhan atau masukan Anda dengan jelas</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="username" value="<?= esc(session()->get('username')) ?>">

                <div class="alert alert-info mb-3">
                    <i class="fas fa-lightbulb"></i>
                    <strong>Tips:</strong> Jelaskan kronologi singkat, tanggal kejadian, dan detail penting lainnya.
                </div>

                <div class="mb-3">
                    <label for="target_role" class="form-label">
                        Ditujukan ke <span style="color: #dc3545;">*</span>
                    </label>
                    <select id="target_role" name="target_role" class="form-select" required>
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
                </div>

                <div class="mb-3">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label for="isi" class="form-label mb-0">
                            Isi Pengaduan <span style="color: #dc3545;">*</span>
                        </label>
                        <small style="color: var(--gray-600);" id="charCount">0 / 1000</small>
                    </div>
                    <textarea
                        id="isi"
                        name="isi"
                        class="form-control auto-resize"
                        rows="4"
                        maxlength="1000"
                        placeholder="Contoh: Tanggal xx/xx/2025 terjadi keterlambatan material..."
                        required></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-send"></i> Kirim Pengaduan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== CHAR COUNTER MODAL =====
        const isiInput = document.getElementById('isi');
        const charCounter = document.getElementById('charCount');

        if (isiInput && charCounter) {
            const updateCounter = () => {
                charCounter.textContent = isiInput.value.length + ' / ' + (isiInput.maxLength || 1000);
            };
            isiInput.addEventListener('input', updateCounter);
            updateCounter();
        }

        // ===== AUTO RESIZE TEXTAREA (modal + reply) =====
        function autoResize(el) {
            el.style.height = 'auto'; // reset dulu
            el.style.height = el.scrollHeight + 'px'; // sesuaikan dgn konten

            // kontrol scrollbar
            if (el.scrollHeight > el.clientHeight) {
                el.style.overflowY = 'auto';
            } else {
                el.style.overflowY = 'hidden';
            }
        }

        // event global untuk semua textarea.auto-resize
        document.addEventListener('input', function(e) {
            if (!e.target.classList || !e.target.classList.contains('auto-resize')) return;
            autoResize(e.target);
        });

        // inisialisasi awal semua textarea.auto-resize yang sudah ada di DOM
        function initAutoResize() {
            document.querySelectorAll('textarea.auto-resize').forEach(autoResize);
        }
        initAutoResize();
        // expose supaya bisa dipanggil setelah AJAX reload
        window.initAutoResize = initAutoResize;

        // ketika modal create dibuka, refresh height textarea modal
        const modalEl = document.getElementById('modalCreatePengaduan');
        if (modalEl) {
            modalEl.addEventListener('shown.bs.modal', function() {
                const ta = modalEl.querySelector('textarea.auto-resize');
                if (ta) autoResize(ta);
            });
        }

        // ===== FILTER LOGIC =====
        const filters = {
            text: document.getElementById('filterText'),
            role: document.getElementById('filterRole'),
            user: document.getElementById('filterUser'),
            from: document.getElementById('filterFrom'),
            to: document.getElementById('filterTo')
        };

        const resultCountEl = document.getElementById('resultCount');
        const emptyStateEl = document.getElementById('emptyState');
        const clearBtn = document.getElementById('btnClearFilter');

        function applyFilter() {
            const q = (filters.text.value || '').toLowerCase().trim();
            const role = (filters.role.value || '').trim();
            const user = (filters.user.value || '').toLowerCase().trim();
            const from = filters.from.value ? new Date(filters.from.value) : null;
            let to = filters.to.value ? new Date(filters.to.value) : null;

            if (to) to.setHours(23, 59, 59, 999); // inklusif

            let visible = 0;
            document.querySelectorAll('.pengaduan-card').forEach(card => {
                const cardRole = (card.dataset.role || '').trim();
                const cardUser = (card.dataset.user || '').toLowerCase().trim();
                const cardDate = card.dataset.date ? new Date(card.dataset.date) : null;
                const search = (card.dataset.search || '').toLowerCase();

                const roleMatch = !role || role === cardRole;
                const userMatch = !user || cardUser.includes(user);
                const textMatch = !q || search.includes(q);

                let dateMatch = true;
                if (cardDate && (from || to)) {
                    if (from && cardDate < from) dateMatch = false;
                    if (to && cardDate > to) dateMatch = false;
                }

                const show = roleMatch && userMatch && textMatch && dateMatch;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (resultCountEl) resultCountEl.textContent = visible;
            if (emptyStateEl) emptyStateEl.style.display = visible === 0 ? 'block' : 'none';
        }

        // expose ke global supaya bisa dipanggil loadPengaduan()
        window.applyFilter = applyFilter;

        Object.values(filters).forEach(filter => {
            if (!filter) return;
            filter.addEventListener('input', applyFilter);
            filter.addEventListener('change', applyFilter);
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                Object.values(filters).forEach(f => {
                    if (!f) return;
                    f.value = '';
                });
                applyFilter();
            });
        }

        // initial filter
        applyFilter();
    });
</script>

<script>
    $(function() {
        // ===== AJAX CREATE PENGADUAN =====
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

                        // reset form + counter
                        $('#formCreate')[0].reset();
                        const $counter = $('#charCount');
                        if ($counter.length) $counter.text('0 / 1000');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal mengirim pengaduan.'
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan koneksi ke server.'
                    });
                }
            });
        });

        // ===== AJAX REPLY PENGADUAN =====
        $(document).on('submit', '.form-reply', function(e) {
            e.preventDefault();

            const id = $(this).data('id');
            if (!id) return;

            $.ajax({
                url: CapacityUrl + 'pengaduan/reply/' + id,
                method: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status === 'success' || res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Balasan terkirim.'
                        });
                        loadPengaduan();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal mengirim balasan.'
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengirim balasan.'
                    });
                }
            });
        });

        // ===== RELOAD LIST PENGADUAN =====
        window.loadPengaduan = function() {
            $.get('<?= base_url($role . "/pengaduan") ?>', function(html) {
                const $html = $(html);
                const $newList = $html.find('#listPengaduan').html();

                $('#listPengaduan').html($newList);

                // re-apply filter setelah reload
                if (typeof window.applyFilter === 'function') {
                    window.applyFilter();
                }

                // re-init auto-resize utk textarea yang baru
                if (typeof window.initAutoResize === 'function') {
                    window.initAutoResize();
                }
            });
        };
    });
</script>

<script>
    let lastPengaduanId = 0;
let lastReplyId = 0;

function initCursorFromDOM() {
  // ambil dari card terakhir (kalau mau)
  const cards = document.querySelectorAll('.pengaduan-card');
  cards.forEach(c => {
    const id = parseInt(c.getAttribute('data-id') || '0', 10);
    if (id > lastPengaduanId) lastPengaduanId = id;
  });

  // kalau reply punya data-id juga
  const replies = document.querySelectorAll('.reply-item[data-reply-id]');
  replies.forEach(r => {
    const rid = parseInt(r.getAttribute('data-reply-id') || '0', 10);
    if (rid > lastReplyId) lastReplyId = rid;
  });
}

function escapeHtml(str) {
  return String(str ?? '')
    .replaceAll('&','&amp;').replaceAll('<','&lt;')
    .replaceAll('>','&gt;').replaceAll('"','&quot;')
    .replaceAll("'","&#039;");
}

function nl2br(str) {
  return escapeHtml(str).replace(/\n/g, '<br>');
}

function renderPengaduanCard(p, currentUser) {
  const username = escapeHtml(p.username);
  const initial = username ? username[0].toUpperCase() : '?';
  const role = escapeHtml(p.display_role || p.target_role);
  const waktu = escapeHtml(p.formatted_time || p.created_at);

  return `
  <div class="pengaduan-card"
       data-id="${p.id_pengaduan}"
       data-role="${escapeHtml(p.target_role)}"
       data-user="${escapeHtml((p.username||'').toLowerCase())}"
       data-date="${escapeHtml(p.date_iso || '')}"
       data-search="${escapeHtml((p.search_blob || '').toLowerCase())}">
    <div class="pengaduan-header">
      <div class="pengaduan-sender">
        <div class="sender-avatar">${escapeHtml(initial)}</div>
        <div class="sender-info">
          <h6>${username} <i class="fa-solid fa-angles-right"></i>
            <span class="badge">${role}</span>
          </h6>
        </div>
      </div>
      <div class="pengaduan-meta">
        <div class="pengaduan-time">
          <i class="fas fa-clock"></i>
          <strong>${waktu}</strong>
        </div>
        <button type="button" class="btn-pdf"
          onclick="window.location.href='${p.pdf_url}'">
          <i class="fas fa-file-pdf"></i> PDF
        </button>
      </div>
    </div>

    <div class="pengaduan-content">${nl2br(p.isi)}</div>

    <hr class="pengaduan-divider">

    <div class="replies-container">
      <div class="no-reply">Belum ada balasan.</div>
    </div>

    <form class="form-reply" data-id="${p.id_pengaduan}">
      <input type="hidden" name="username" value="${escapeHtml(currentUser)}">
      <textarea name="isi" class="auto-resize" placeholder="Tulis balasan..." required></textarea>
      <button type="submit"><i class="fas fa-paper-plane"></i></button>
    </form>
  </div>`;
}

function renderReplyBubble(r, currentUser) {
  const isMe = (String(r.username || '').toLowerCase() === String(currentUser || '').toLowerCase());
  return `
  <div class="reply-row ${isMe ? 'reply-row-me' : 'reply-row-other'}">
    <div class="reply-item ${isMe ? 'reply-me' : 'reply-other'}" data-reply-id="${r.id_reply}">
      <div class="reply-author"><i class="fas fa-reply"></i> ${escapeHtml(r.username)}</div>
      <div class="reply-text">${nl2br(r.isi)}</div>
      <div class="reply-time">${escapeHtml(r.created_at)}</div>
    </div>
  </div>`;
}

function upsertReplyToCard(reply, currentUser) {
  const card = document.querySelector(`.pengaduan-card[data-id="${reply.id_pengaduan}"]`);
  if (!card) return; // kalau card belum ada, bisa skip atau nanti handle

  const container = card.querySelector('.replies-container');
  if (!container) return;

  // hilangkan "Belum ada balasan"
  const noReply = container.querySelector('.no-reply');
  if (noReply) noReply.remove();

  // cegah duplikasi
  if (container.querySelector(`.reply-item[data-reply-id="${reply.id_reply}"]`)) return;

  container.insertAdjacentHTML('beforeend', renderReplyBubble(reply, currentUser));
}

function startPollingPengaduan({ role, currentUser }) {
  initCursorFromDOM();

  async function tick() {
    try {
      const url = `${CapacityUrl}pengaduan/fetchNew?last_id=${lastPengaduanId}&last_reply_id=${lastReplyId}&role=${encodeURIComponent(role)}` +
        `&username=${encodeURIComponent(currentUser)}`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const json = await res.json();

      if (!json || !json.success) return;

      // 1) pengaduan baru -> prepend
      if (Array.isArray(json.data) && json.data.length) {
        const list = document.getElementById('listPengaduan');
        json.data
          .sort((a,b) => a.id_pengaduan - b.id_pengaduan)
          .forEach(p => {
            if (document.querySelector(`.pengaduan-card[data-id="${p.id_pengaduan}"]`)) return;
            list.insertAdjacentHTML('afterbegin', renderPengaduanCard(p, currentUser));
          });

        // re-init textarea resize + re-apply filter
        window.initAutoResize?.();
        window.applyFilter?.();
      }

      // 2) reply baru -> append ke card yg sesuai
      if (Array.isArray(json.replies) && json.replies.length) {
        json.replies
          .sort((a,b) => a.id_reply - b.id_reply)
          .forEach(r => upsertReplyToCard(r, currentUser));

        window.applyFilter?.();
      }

      // update cursor
      if (json.max_id) lastPengaduanId = Math.max(lastPengaduanId, Number(json.max_id));
      if (json.max_reply_id) lastReplyId = Math.max(lastReplyId, Number(json.max_reply_id));

    } catch (e) {
      console.error('poll error', e);
    }
  }

  tick();
  setInterval(tick, 5000);
}

// panggil ini setelah DOM ready
startPollingPengaduan({
  role: '<?= esc($role) ?>',                // atau session role
  currentUser: '<?= esc(session()->get('username')) ?>'
});


</script>



<?= $this->endSection() ?>