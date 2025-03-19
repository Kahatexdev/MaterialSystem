<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between">
                    <h6>Pengiriman Area</h6>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPengiriman">Tambah Pengiriman</button>
                </div>
                <div class="card-body px-3 py-2">
                    <div class="table-responsive">
                        <table id="pengirimanAreaTable" class="table table-bordered align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">Tanggal</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">Nama Barang</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">Jumlah</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">Area Tujuan</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $dataPengirimanArea = [
                                    [
                                        'id' => 1,
                                        'tanggal' => '2025-03-10',
                                        'nama_barang' => 'Produk A',
                                        'jumlah' => 100,
                                        'area_tujuan' => 'Area Distribusi Jakarta',
                                    ],
                                    [
                                        'id' => 2,
                                        'tanggal' => '2025-03-11',
                                        'nama_barang' => 'Produk B',
                                        'jumlah' => 150,
                                        'area_tujuan' => 'Area Distribusi Bandung',
                                    ],
                                    [
                                        'id' => 3,
                                        'tanggal' => '2025-03-12',
                                        'nama_barang' => 'Produk C',
                                        'jumlah' => 200,
                                        'area_tujuan' => 'Area Distribusi Surabaya',
                                    ],
                                    [
                                        'id' => 4,
                                        'tanggal' => '2025-03-13',
                                        'nama_barang' => 'Produk D',
                                        'jumlah' => 50,
                                        'area_tujuan' => 'Area Distribusi Bali',
                                    ],
                                    [
                                        'id' => 5,
                                        'tanggal' => '2025-03-14',
                                        'nama_barang' => 'Produk E',
                                        'jumlah' => 300,
                                        'area_tujuan' => 'Area Distribusi Medan',
                                    ],
                                ];
                                ?>

                                <?php foreach ($dataPengirimanArea as $pengiriman): ?>
                                    <tr>
                                        <td><?= $pengiriman['tanggal'] ?></td>
                                        <td><?= $pengiriman['nama_barang'] ?></td>
                                        <td><?= $pengiriman['jumlah'] ?></td>
                                        <td><?= $pengiriman['area_tujuan'] ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditPengiriman"
                                                data-id="<?= $pengiriman['id'] ?>"
                                                data-tanggal="<?= $pengiriman['tanggal'] ?>"
                                                data-nama_barang="<?= $pengiriman['nama_barang'] ?>"
                                                data-jumlah="<?= $pengiriman['jumlah'] ?>"
                                                data-area_tujuan="<?= $pengiriman['area_tujuan'] ?>">
                                                Edit
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

<!-- Modal Tambah Pengiriman -->
<div class="modal fade" id="modalTambahPengiriman" tabindex="-1" aria-labelledby="modalTambahPengirimanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahPengirimanLabel">Tambah Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($role . '/warehouseCov/tambah_pengiriman_area') ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                    </div>
                    <div class="mb-3">
                        <label for="area_tujuan" class="form-label">Area Tujuan</label>
                        <input type="text" class="form-control" id="area_tujuan" name="area_tujuan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Pengiriman -->
<div class="modal fade" id="modalEditPengiriman" tabindex="-1" aria-labelledby="modalEditPengirimanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPengirimanLabel">Edit Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($role . '/warehouseCov/edit_pengiriman_area') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="edit_nama_barang" name="nama_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="edit_jumlah" name="jumlah" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_area_tujuan" class="form-label">Area Tujuan</label>
                        <input type="text" class="form-control" id="edit_area_tujuan" name="area_tujuan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#pengirimanAreaTable').DataTable({});

        // Populate Edit Modal with data
        $('#modalEditPengiriman').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var id = button.data('id');
            var tanggal = button.data('tanggal');
            var nama_barang = button.data('nama_barang');
            var jumlah = button.data('jumlah');
            var area_tujuan = button.data('area_tujuan');

            var modal = $(this);
            modal.find('#edit_id').val(id);
            modal.find('#edit_tanggal').val(tanggal);
            modal.find('#edit_nama_barang').val(nama_barang);
            modal.find('#edit_jumlah').val(jumlah);
            modal.find('#edit_area_tujuan').val(area_tujuan);
        });
    });
</script>

<?php $this->endSection(); ?>