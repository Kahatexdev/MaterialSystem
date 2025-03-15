<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?= $title ?></h4>
            <button class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addPemasukanModal">
                <i class="fas fa-plus"></i> Tambah Pemasukan
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="pemasukanTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Supplier</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dataPemasukan = [
                            [
                                'id' => 1,
                                'tanggal' => '2025-03-01',
                                'nama_barang' => 'Kabel Listrik',
                                'jumlah' => 100,
                                'supplier' => 'PT. Listrik Jaya',
                            ],
                            [
                                'id' => 2,
                                'tanggal' => '2025-03-03',
                                'nama_barang' => 'Pipa Besi',
                                'jumlah' => 50,
                                'supplier' => 'CV. Besi Kuat',
                            ],
                            [
                                'id' => 3,
                                'tanggal' => '2025-03-05',
                                'nama_barang' => 'Baut dan Mur',
                                'jumlah' => 200,
                                'supplier' => 'UD. Perkakas Hebat',
                            ],
                            [
                                'id' => 4,
                                'tanggal' => '2025-03-07',
                                'nama_barang' => 'Kaca Tempered',
                                'jumlah' => 30,
                                'supplier' => 'PT. Kaca Modern',
                            ],
                            [
                                'id' => 5,
                                'tanggal' => '2025-03-09',
                                'nama_barang' => 'Cat Tembok',
                                'jumlah' => 80,
                                'supplier' => 'CV. Warna Cerah',
                            ],
                        ];
                        ?>
                        <?php foreach ($dataPemasukan as $data): ?>
                            <tr>
                                <td><?= $data['id'] ?></td>
                                <td><?= $data['tanggal'] ?></td>
                                <td><?= $data['nama_barang'] ?></td>
                                <td><?= $data['jumlah'] ?></td>
                                <td><?= $data['supplier'] ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="editPemasukan(<?= $data['id'] ?>)">
                                        <i class="fas fa-edit"></i> Edit
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

<!-- Modal Tambah Pemasukan -->
<div class="modal fade" id="addPemasukanModal" tabindex="-1" aria-labelledby="addPemasukanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url($role . '/warehouseCov/addPemasukan') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPemasukanModalLabel">Tambah Pemasukan</h5>
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
                        <label for="supplier" class="form-label">Supplier</label>
                        <input type="text" name="supplier" class="form-control" id="supplier" required>
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
        $('#pemasukanTable').DataTable();

        // Function to edit pemasukan (Placeholder function)
        window.editPemasukan = function(id) {
            alert('Edit Pemasukan ID: ' + id);
            // Implementasi modal edit akan dilakukan di sini
        }
    });
</script>

<?php $this->endSection(); ?>