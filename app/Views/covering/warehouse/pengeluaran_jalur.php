<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Pengeluaran Jalur Gudang</h6>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPengeluaranJalurModal">
                        <i class="fas fa-plus"></i> Tambah Pengeluaran
                    </button>
                </div>
                <div class="card-body px-3 py-2">
                    <div class="table-responsive">
                        <table id="pengeluaranJalurTable" class="table table-striped table-bordered align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">Tanggal</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">Nama Barang</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">Jumlah</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">Jalur Gudang</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">Tujuan</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $dataPengeluaranJalur = [
                                    [
                                        'id' => 1,
                                        'tanggal' => '2025-03-10',
                                        'nama_barang' => 'Bahan Baku A',
                                        'jumlah' => 50,
                                        'jalur_gudang' => 'Gudang A',
                                        'tujuan' => 'Produksi Line 1',
                                    ],
                                    [
                                        'id' => 2,
                                        'tanggal' => '2025-03-11',
                                        'nama_barang' => 'Bahan Baku B',
                                        'jumlah' => 30,
                                        'jalur_gudang' => 'Gudang B',
                                        'tujuan' => 'Produksi Line 2',
                                    ],
                                    [
                                        'id' => 3,
                                        'tanggal' => '2025-03-12',
                                        'nama_barang' => 'Komponen C',
                                        'jumlah' => 20,
                                        'jalur_gudang' => 'Gudang A',
                                        'tujuan' => 'Area Perakitan',
                                    ],
                                    [
                                        'id' => 4,
                                        'tanggal' => '2025-03-13',
                                        'nama_barang' => 'Alat Pendukung',
                                        'jumlah' => 10,
                                        'jalur_gudang' => 'Gudang C',
                                        'tujuan' => 'Maintenance',
                                    ],
                                    [
                                        'id' => 5,
                                        'tanggal' => '2025-03-14',
                                        'nama_barang' => 'Bahan Baku D',
                                        'jumlah' => 40,
                                        'jalur_gudang' => 'Gudang B',
                                        'tujuan' => 'Produksi Line 3',
                                    ],
                                ];
                                ?>

                                <?php foreach ($dataPengeluaranJalur as $pengeluaran): ?>
                                    <tr>
                                        <td><?= $pengeluaran['tanggal'] ?></td>
                                        <td><?= $pengeluaran['nama_barang'] ?></td>
                                        <td><?= $pengeluaran['jumlah'] ?></td>
                                        <td><?= $pengeluaran['jalur_gudang'] ?></td>
                                        <td><?= $pengeluaran['tujuan'] ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" onclick="editPengeluaranJalur(<?= $pengeluaran['id'] ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="deletePengeluaranJalur(<?= $pengeluaran['id'] ?>)">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pengeluaran Jalur -->
<div class="modal fade" id="addPengeluaranJalurModal" tabindex="-1" aria-labelledby="addPengeluaranJalurModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url($role . '/warehouseCov/addPengeluaranJalur') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPengeluaranJalurModalLabel">Tambah Pengeluaran Jalur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" id="tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" id="nama_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" id="jumlah" required>
                    </div>
                    <div class="mb-3">
                        <label for="jalur_gudang" class="form-label">Jalur Gudang</label>
                        <input type="text" name="jalur_gudang" class="form-control" id="jalur_gudang" placeholder="Contoh: Gudang A atau Gudang B" required>
                    </div>
                    <div class="mb-3">
                        <label for="tujuan" class="form-label">Tujuan</label>
                        <input type="text" name="tujuan" class="form-control" id="tujuan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#pengeluaranJalurTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json"
            }
        });

        // Placeholder function for edit pengeluaran jalur
        window.editPengeluaranJalur = function(id) {
            alert('Edit Pengeluaran Jalur ID: ' + id);
            // Implementasi modal edit akan dilakukan di sini
        };

        // Placeholder function for delete pengeluaran jalur
        window.deletePengeluaranJalur = function(id) {
            if (confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?')) {
                // Implementasi proses hapus akan dilakukan di sini
                alert('Pengeluaran Jalur ID ' + id + ' telah dihapus.');
            }
        };
    });
</script>



<?php $this->endSection(); ?>