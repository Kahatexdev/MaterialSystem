<?php $this->extend($role . '/pengaduan/header'); ?>
<?= $this->section('content') ?>

<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Daftar Pengaduan</h4>
        <!-- Tombol buka modal -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreatePengaduan">
            + Buat Pengaduan
        </button>
    </div>

    <!-- Jika tidak ada pengaduan -->
    <?php if (empty($pengaduan)) : ?>
        <div class="alert alert-info text-center">
            Tidak ada pesan/aduan.
        </div>
    <?php else : ?>
        <?php foreach ($pengaduan as $p) : ?>
            <div class="card mb-3" id="listPengaduan">
                <div class="card-body">
                    <?php
                    $timestamp = strtotime($p['created_at']);
                    $formattedDate = '<strong>' . date('l, d/m/Y', $timestamp) . '</strong> (' . date('H:i', $timestamp) . ')';
                    $roleMap = [
                        'sudo'     => 'monitoring',
                        'aps'      => 'Planner',
                        'planning' => 'PPC',
                        'user'     => 'Area'
                    ];

                    $displayRole = $roleMap[$p['target_role']] ?? $p['target_role'];
                    ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-1">
                            <strong><?= esc($p['username']) ?></strong> →
                            <span class="badge bg-secondary"><?= esc($displayRole) ?></span>
                        </h6>
                        <small class="text-muted"><?= $formattedDate ?></small>
                    </div>
                    <p><?= nl2br(esc($p['isi'])) ?></p>


                    <hr class="my-2">

                    <!-- Reply list -->
                    <?php if (!empty($replies[$p['id_pengaduan']])) : ?>
                        <?php foreach ($replies[$p['id_pengaduan']] as $r) : ?>
                            <div class="border-start ps-2 mb-2">
                                <strong><?= esc($r['username']) ?></strong>:
                                <?= nl2br(esc($r['isi'])) ?>
                                <div><small class="text-muted"><?= $r['created_at'] ?></small></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="text-muted small">Belum ada balasan.</div>
                    <?php endif; ?>

                    <!-- Form reply -->
                    <form class="formReply" data-id="<?= $p['id_pengaduan'] ?>" class="mt-2">
                        <div class="input-group">
                            <input type="hidden" name="username" value="<?= session()->get('username') ?>">
                            <input type="text" name="isi" class="form-control" placeholder="Tulis balasan..." required>
                            <button class="btn btn-outline-primary" type="submit">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Create Pengaduan -->
<div class="modal fade" id="modalCreatePengaduan" tabindex="-1" aria-labelledby="modalCreatePengaduanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formCreate" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreatePengaduanLabel">Buat Pengaduan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="username" value="<?= session()->get('username') ?>">
                <!-- Pilih target role -->
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
                    </select>
                </div>

                <!-- Isi pengaduan -->
                <div class="mb-3">
                    <label for="isi" class="form-label">Isi Pengaduan</label>
                    <textarea name="isi" id="isi" class="form-control" rows="3" placeholder="Tulis aduan Anda..." required></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Kirim</button>
            </div>
        </form>
    </div>
</div>
<script>
    $('#formCreate').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: CapacityUrl + 'pengaduan/create',
            method: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    $(document).ready(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            html: '<?= session()->getFlashdata('success') ?>',
                        });
                    });
                    loadPengaduan(); // reload list
                    $('#modalCreatePengaduan').modal('hide'); // close modal
                } else {
                    alert('Gagal mengirim: ' + res.message);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Terjadi kesalahan koneksi ke server.');
            }
        });
    });

    $(document).on('submit', '.formReply', function(e) {
        e.preventDefault();

        let id = $(this).data('id');

        $.ajax({
            url: CapacityUrl + 'pengaduan/reply/' + id,
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        html: '<?= session()->getFlashdata('success') ?>',
                    });
                });
                loadPengaduan();
            },
            error: function() {
                alert('Gagal mengirim balasan.');
            }
        });
    });

    function loadPengaduan() {
        $.get('<?= base_url($role . "/pengaduan") ?>', function(html) {
            $('#listPengaduan').html($(html).find('#listPengaduan').html());
        });
    }
</script>
<?= $this->endSection() ?>