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

    <div class="card">
        <div class="card-body table-responsive">
            <table id="stokTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Mesin</th>
                        <th>Jenis Barang</th>
                        <th>DR</th>
                        <th>Color</th>
                        <th>Code</th>
                        <th>LMD</th>
                        <th>Cones</th>
                        <th>Kg</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody><?php foreach ($stok as $i => $item): ?><tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($item['jenis_mesin']) ?></td>
                            <td><?= esc($item['jenis']) ?></td>
                            <td><?= esc($item['dr']) ?></td>
                            <td><?= esc($item['color']) ?></td>
                            <td><?= esc($item['code']) ?></td>
                            <td><?= esc($item['lmd']) ?></td>
                            <td><?= esc($item['ttl_cns'] ?? '-') ?></td>
                            <td><?= esc(number_format($item['ttl_kg'], 2)) ?>Kg</td>
                            <td><button class="btn btn-warning" onclick="editItem(<?= $item['id_covering_stock'] ?>)"><i class="fas fa-edit"></i>Edit </button><button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $item['id_covering_stock'] ?>)"><i class="fas fa-trash"></i>Hapus </button></td>
                        </tr><?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Edit Item Modal -->
    <div class="modal fade" id="editStockModal" tabindex="-1" aria-labelledby="editStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Detail Barang</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editStockForm">
                        <input type="hidden" id="editStockItemId" name="id_covering_stock">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3"><label class="form-label">Jenis Barang</label><input type="text" class="form-control" id="editJenis" name="jenis" required></div>
                                <div class="mb-3"><label class="form-label">Jenis Mesin</label><input type="text" class="form-control" id="editJenisMesin" name="jenis_mesin"></div>
                                <div class="mb-3"><label class="form-label">DR (Daurasio)</label><input type="text" class="form-control" id="editDr" name="dr"></div>
                                <div class="mb-3"><label class="form-label">Jenis Cover</label><select class="form-select" id="editJenisCover" name="jenis_cover" required>
                                        <option value="">Pilih...</option>
                                        <option value="SINGLE">SINGLE</option>
                                        <option value="DOUBLE">DOUBLE</option>
                                    </select></div>
                                <div class="mb-3"><label class="form-label">Jenis Benang</label><input type="text" class="form-control" id="editJenisBenang" name="jenis_benang" required></div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3"><label class="form-label">Warna</label><input type="text" class="form-control" id="editColor" name="color" required></div>
                                <div class="mb-3"><label class="form-label">Kode</label><input type="text" class="form-control" id="editCode" name="code" required></div>
                                <div class="mb-3"><label class="form-label">Stok (Kg)</label><input type="number" class="form-control" id="editTtlKg" readonly></div>
                                <div class="mb-3"><label class="form-label">Cones</label><input type="number" class="form-control" id="editTtlCns" readonly></div>
                                <div class="mb-3"><label class="form-label">LMD</label><br>
                                    <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" id="editLmd1" name="lmd[]" value="L"><label>L</label></div>
                                    <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" id="editLmd2" name="lmd[]" value="M"><label>M</label></div>
                                    <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" id="editLmd3" name="lmd[]" value="D"><label>D</label></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn bg-gradient-info">Simpan Perubahan</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.21.1/dist/bootstrap-table.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#stokTable').DataTable({
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