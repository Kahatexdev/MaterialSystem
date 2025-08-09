<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.21.1/dist/bootstrap-table.min.css" />
<div class="container-fluid py-4">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => Swal.fire('Success!', '<?= session()->getFlashdata('success') ?>', 'success'));
        </script>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => Swal.fire('Error!', '<?= session()->getFlashdata('error') ?>', 'error'));
        </script>
    <?php endif; ?>

    <!-- ALERT FLASHDATA -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show mx-3 mt-3" role="alert">
            <?= session()->getFlashdata('warning') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Stock Covering</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data <?= $title ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="card px-3 py-3 mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="stockTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis</th>
                            <th>Denier</th>
                            <th>Warna</th>
                            <th>Kode Warna</th>
                            <th>Stock</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($spandexBB as $bb) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $bb['jenis_benang'] ?></td>
                                <td><?= $bb['denier'] ?></td>
                                <td><?= $bb['warna'] ?></td>
                                <td><?= $bb['kode'] ?></td>
                                <td><?= $bb['kg'] ?> Kg</td>
                                <td><?= $bb['keterangan'] ?></td>
                                <td>
                                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $bb['idstockbb'] ?>"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-delete" data-id="<?= $bb['idstockbb'] ?>"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Edit Item Modal -->
        <?php foreach ($spandexBB as $list) : ?>
            <div class="modal fade" id="editModal<?= $list['idstockbb'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header text-white">
                            <h5 class="modal-title" id="editModalLabel">Update Stock Bahan Baku</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="<?= base_url($role . '/warehouseBB/update/' . $list['idstockbb']) ?>" method="POST">
                            <?= csrf_field(); ?>
                            <input type="hidden" step="0.01" class="form-control" name="kg" value="<?= $list['kg'] ?>" placeholder="Masukkan Stock (Kg)" required>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Jenis Benang</label>
                                        <select class="form-select" name="jenis_benang" required>
                                            <option value="NYLON" <?= $list['jenis_benang'] == 'NYLON' ? 'selected' : '' ?>>NYLON</option>
                                            <option value="POLYESTER" <?= $list['jenis_benang'] == 'POLYESTER' ? 'selected' : '' ?>>POLYESTER</option>
                                            <option value="SPANDEX" <?= $list['jenis_benang'] == 'SPANDEX' ? 'selected' : '' ?>>SPANDEX</option>
                                            <option value="KARET" <?= $list['jenis_benang'] == 'KARET' ? 'selected' : '' ?>>KARET</option>
                                            <option value="RECYCLED POLYESTER" <?= $list['jenis_benang'] == 'RECYCLED POLYESTER' ? 'selected' : '' ?>>RECYCLED POLYESTER</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Denier</label>
                                        <input type="text" class="form-control" name="denier" value="<?= $list['denier'] ?>" placeholder="Masukkan Denier" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Warna</label>
                                        <input type="text" class="form-control" name="warna" value="<?= $list['warna'] ?>" placeholder="Masukkan Warna" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Kode Warna</label>
                                        <input type="text" class="form-control" name="kode" value="<?= $list['kode'] ?>" placeholder="Masukkan Kode Warna" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Stock (Kg)</label>
                                        <input type="text" class="form-control" name="stock" value="<?= $list['kg'] ?>" placeholder="Masukkan Stock" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Keterangan</label>
                                        <textarea name="keterangan" id="" class="form-control" placeholder="Masukkan Keterangan"><?= $list['keterangan'] ?></textarea>
                                    </div>

                                    <!-- Hidden values for reference -->
                                    <input type="hidden" name="idstockbb" value="<?= $list['idstockbb'] ?>">

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-info">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.21.1/dist/bootstrap-table.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#stockTable').DataTable({
            // contoh opsi:
            lengthMenu: [10, 25, 50, 100],
            pageLength: 25,
            order: [
                [1, 'asc']
            ], // default sort kolom Jenis Barang
            columnDefs: [{
                targets: [0, 7], // kolom No & Aksi tidak bisa di-sort
                orderable: false
            }],
        });
    });
</script>
<script>
    const BASE_URL = "<?= base_url() ?>";

    document.addEventListener('DOMContentLoaded', function() {

        document.getElementById("editStockForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const form = e.target;
            const payload = {
                id_covering_stock: form.id_covering_stock.value,
                jenis: form.jenis.value,
                jenis_mesin: form.jenis_mesin.value,
                dr: form.dr.value,
                jenis_cover: form.jenis_cover.value,
                jenis_benang: form.jenis_benang.value,
                color: form.color.value,
                code: form.code.value,
                lmd: Array.from(form.querySelectorAll("input[name='lmd[]']:checked")).map(cb => cb.value)
            };

            fetch(`${BASE_URL}covering/warehouse/updateEditStock`, {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload),
                })
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        Swal.fire('Berhasil', 'Data stok berhasil diupdate!', 'success').then(() => location.reload());
                    } else {
                        Swal.fire("Gagal!", result.message || "Tidak dapat memperbarui.", "error");
                    }
                })
                .catch(() => Swal.fire("Error!", "Gagal mengupdate data.", "error"));
        });
    });

    function editItem(id) {
        fetch(`${BASE_URL}covering/warehouse/getStock/${id}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) return Swal.fire("Gagal!", "Data tidak ditemukan", "error");

                const stock = data.stock;
                const form = document.getElementById('editStockForm');
                form.id_covering_stock.value = stock.id_covering_stock;
                form.jenis.value = stock.jenis;
                form.jenis_mesin.value = stock.jenis_mesin || '';
                form.dr.value = stock.dr || '';
                form.jenis_cover.value = stock.jenis_cover || '';
                form.jenis_benang.value = stock.jenis_benang || '';
                form.color.value = stock.color;
                form.code.value = stock.code;
                form.querySelector('#editTtlKg').value = stock.ttl_kg;
                form.querySelector('#editTtlCns').value = stock.ttl_cns;

                form.querySelectorAll("input[name='lmd[]']").forEach(cb => cb.checked = false);
                if (stock.lmd) {
                    stock.lmd.split(',').forEach(val => {
                        const cb = form.querySelector(`input[value='${val.trim()}']`);
                        if (cb) cb.checked = true;
                    });
                }

                const modal = new bootstrap.Modal(document.getElementById('editStockModal'));
                modal.show();
            })
            .catch(() => Swal.fire("Error!", "Gagal mengambil data.", "error"));
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: "Apakah Anda yakin ingin menghapus item ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) return;

            const tokenName = '<?= csrf_token() ?>'; // misal: csrf_token()
            const tokenValue = '<?= csrf_hash() ?>'; // misal: csrf_hash()

            fetch(`${BASE_URL}covering/warehouse/deleteStokBarangJadi/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        [tokenName]: tokenValue
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (res.ok && data.success) {
                        Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error!', data.message || 'Gagal menghapus item.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error!', 'Gagal menghapus item.', 'error');
                });
        });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('importForm');
        const btn = document.getElementById('importSubmit');
        const spinner = btn.querySelector('.spinner-border');
        const text = btn.querySelector('.btn-text');

        form.addEventListener('submit', function() {
            // disable tombol agar user tidak klik dua kali
            btn.disabled = true;
            // tampilkan spinner
            spinner.classList.remove('d-none');
            // ubah teks tombol
            text.textContent = ' Importing…';
            // biarkan form submit berjalan normal
        });
    });
</script>





<?php $this->endSection(); ?>