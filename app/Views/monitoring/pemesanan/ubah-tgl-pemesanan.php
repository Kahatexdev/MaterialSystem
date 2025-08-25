<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>

<style>
    .note-section {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .note-section strong {
        color: #0c4a6e;
    }

    .note-section ul {
        margin: 0.5rem 0 0 0;
        padding-left: 1.2rem;
        color: #0369a1;
    }
</style>

<div class="container-fluid py-4">
    <div class="card card-frame">
        <div class="card-body p-4">
            <div class="">
                <h5 class="font-weight-bolder"><i class="fas fa-calendar-edit me-2"></i>Ubah Tanggal Pemesanan</h5>
            </div>
            <div class="bg-light p-3 rounded mt-3">
                <div class="text-dark">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Tanggal Pesan: <?= $hari . ', ' . $today ?></span>
                </div>

                <div class="note-section">
                    <strong><i class="fas fa-info-circle me-1"></i>Informasi Penting:</strong>
                    <ul>
                        <li><strong>Hari Kamis:</strong> Pemesanan 2 tanggal untuk Spandex & Karet</li>
                        <li><strong>Hari Jumat:</strong> Pemesanan 2 tanggal untuk Benang</li>
                        <li><strong>Hari Sabtu:</strong> Pemesanan 2 tanggal untuk Nylon</li>
                    </ul>
                </div>
            </div>

            <!-- Seluruh Area -->
            <div class="seluruh-area mt-4">
                <div class="">
                    <h5 class="font-weight-bolder"><i class="fas fa-calendar-alt me-2"></i>Tanggal Default Pemesanan Seluruh Area</h5>
                </div>
                <form action="<?= base_url($role . '/pemesanan/updateTglSeluruhArea') ?>" method="post">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="">Area</label>
                            <input type="text" class="form-control " value="SELURUH AREA" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="">Tanggal Pemesanan</label>
                            <input type="date" class="form-control " value="">
                        </div>
                        <div class="col-md-2">
                            <label class="">Tanggal Pakai Spandex</label>
                            <input type="date" class="form-control " value="">
                        </div>
                        <div class="col-md-2">
                            <label class="">Tanggal Pakai Karet</label>
                            <input type="date" class="form-control " value="">
                        </div>
                        <div class="col-md-2">
                            <label class="">Tanggal Pakai Benang</label>
                            <input type="date" class="form-control " value="">
                        </div>
                        <div class="col-md-2">
                            <label class="">Tanggal Pakai Nylon</label>
                            <input type="date" class="form-control " value="">
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-end align-items-end">
                        <button class="btn btn-dark btn-lg" type="submit"><i class="fas fa-save me-1"></i>Simpan</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive mt-3">
                <div class="">
                    <h5 class="font-weight-bolder"><i class="fas fa-edit me-2"></i>Mengubah Tanggal Pemesanan Area Tertentu</h5>
                </div>
                <form action="<?= base_url($role . '/pemesanan/updateTglAreaTertentu') ?>">
                    <table id="table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Area</th>
                                <th>Tanggal Pemesanan</th>
                                <th>Tanggal Pakai Spandex</th>
                                <th>Tanggal Pakai Karet</th>
                                <th>Tanggal Pakai Benang</th>
                                <th>Tanggal Pakai Nylon</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="area" id="" class="form-control">
                                        <option value="">Pilih Area</option>
                                        <option value="">KK1A</option>
                                        <option value="">KK1B</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="date" class="form-control" value="">
                                </td>
                                <td>
                                    <input type="date" class="form-control" value="">
                                </td>
                                <td>
                                    <input type="date" class="form-control" value="">
                                </td>
                                <td>
                                    <input type="date" class="form-control" value="">
                                </td>
                                <td>
                                    <input type="date" class="form-control" value="">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-info btn-md" id="addRow"><i class="fas fa-plus"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end align-items-end">
                        <button type="submit" class="btn btn-dark btn-lg"><i class="fas fa-save me-1"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('table');
        const addRow = document.getElementById('addRow');

        addRow.addEventListener('click', function() {
            const newRow = table.insertRow();
            newRow.innerHTML = `
            <td>
                <select name="area" class="form-control">
                    <option value="">Pilih Area</option>
                    <option value="">KK1A</option>
                    <option value="">KK1B</option>
                </select>
            </td>
            <td><input type="date" class="form-control" value=""></td>
            <td><input type="date" class="form-control" value=""></td>
            <td><input type="date" class="form-control" value=""></td>
            <td><input type="date" class="form-control" value=""></td>
            <td><input type="date" class="form-control" value=""></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-md remove-row"><i class="fas fa-minus"></i></button>
            </td>
        `;

            // Tambahkan event listener untuk tombol hapus pada baris baru
            newRow.querySelector('.remove-row').addEventListener('click', function() {
                table.deleteRow(newRow.rowIndex);
            });
        });

        // Event listener untuk tombol hapus pada baris awal
        table.querySelector('.remove-row')?.addEventListener('click', function() {
            const row = this.closest('tr');
            table.deleteRow(row.rowIndex);
        });
    });
</script>

<?php $this->endSection(); ?>