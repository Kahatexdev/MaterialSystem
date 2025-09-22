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


    .reply-textarea:focus {
        box-shadow: var(--ring);
        border-color: var(--brand);
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
            <?php foreach ($pengaduan as $p) : ?>
                <?php
                $timestamp = strtotime($p['created_at']);
                $formattedDate = '<strong>' . date('l, d/m/Y', $timestamp) . '</strong> (' . date('H:i', $timestamp) . ')';
                $roleMap = [
                    'sudo'     => 'Monitoring Planning & Produksi',
                    'aps'      => 'Planner',
                    'planning' => 'PPC',
                    'user'     => 'Area',
                    'capacity' => 'Capacity',
                    'rosso'    => 'Rosso',
                    'gbn'      => 'GBN',
                    'monitoring' => 'Monitoring Bahan Baku',
                ];
                $displayRole = $roleMap[$p['target_role']] ?? $p['target_role'];
                $roleClass = 'role-' . preg_replace('/[^a-z0-9]/', '', strtolower($p['target_role']));
                ?>
                <div class="card complaint-card mb-3" data-complaint-id="<?= $p['id_pengaduan'] ?>">
                    <div class="card-body timeline-accent">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <h6 class="mb-1">
                                    <span class="fw-bold"><?= esc($p['username']) ?></span>
                                    <i class="bi bi-arrow-right-short mx-1 opacity-50"></i>
                                    <span class="badge role-badge <?= $roleClass ?>">
                                        <?= esc($displayRole) ?>
                                    </span>
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

                        <!-- Replies -->
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

                        <!-- Reply form -->
                        <!-- Reply form -->
                        <form class="formReply mt-3" data-id="<?= $p['id_pengaduan'] ?>">
                            <div class="d-flex gap-2 align-items-start">
                                <textarea name="isi" class="form-control reply-textarea auto-resize" rows="1" placeholder="Tulis balasan..." minlength="2" required></textarea>
                                <button class="btn btn-brand text-white px-3" type="submit">
                                    <i class="bi bi-send-fill"></i>
                                </button>
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
                        <option value="sudo">Monitoring Planning & Produksi</option>
                        <option value="monitoring">Monitoring Bahan Baku</option>
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
            url: 'http://127.0.0.1/CapacityApps/public/api/pengaduan/create',
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
            url: 'http://127.0.0.1/CapacityApps/public/api/pengaduan/reply/' + id,
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

    // RELOAD LIST
    function loadPengaduan() {
        // ambil ulang view & ganti isi container list
        $.get('<?= base_url($role . "/pengaduan") ?>', function(html) {
            const $newList = $(html).find('#pengaduanList').html();
            if ($newList) {
                $('#pengaduanList').html($newList);
            }
        });
    }
</script>

<?= $this->endSection() ?>