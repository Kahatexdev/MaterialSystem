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
                    <span>Tanggal Pesan: <?= $today . ', ' . $date ?></span>
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
                <form action="<?= base_url($role . '/pemesanan/updateRangeSeluruhArea') ?>" method="post">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="">Area</label>
                            <input type="text" class="form-control" value="SELURUH AREA" name="allArea" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="">Hari Pemesanan</label>
                            <select name="days" id="daysAllArea" class="form-select">
                                <option value="">Pilih Hari</option>
                                <option value="Monday" <?= (isset($day) && $day == 'Monday') ? 'selected' : '' ?>>Monday</option>
                                <option value="Tuesday" <?= (isset($day) && $day == 'Tuesday') ? 'selected' : '' ?>>Tuesday</option>
                                <option value="Wednesday" <?= (isset($day) && $day == 'Wednesday') ? 'selected' : '' ?>>Wednesday</option>
                                <option value="Thursday" <?= (isset($day) && $day == 'Thursday') ? 'selected' : '' ?>>Thursday</option>
                                <option value="Friday" <?= (isset($day) && $day == 'Friday') ? 'selected' : '' ?>>Friday</option>
                                <option value="Saturday" <?= (isset($day) && $day == 'Saturday') ? 'selected' : '' ?>>Saturday</option>
                                <option value="Sunday" <?= (isset($day) && $day == 'Sunday') ? 'selected' : '' ?>>Sunday</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="">Jumlah Tanggal Spandex</label>
                            <input type="number" class="form-control" name="range_spandex" value="<?= $rangeTgl['range_spandex'] ?? '' ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="">Jumlah Tanggal Karet</label>
                            <input type="number" class="form-control" name="range_karet" value="<?= $rangeTgl['range_karet'] ?? '' ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="">Jumlah Tanggal Benang</label>
                            <input type="number" class="form-control" name="range_benang" value="<?= $rangeTgl['range_benang'] ?? '' ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="">Jumlah Tanggal Nylon</label>
                            <input type="number" class="form-control" name="range_nylon" value="<?= $rangeTgl['range_nylon'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-end align-items-end">
                        <button class="btn btn-dark btn-lg" type="submit"><i class="fas fa-save me-1"></i>Update</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive mt-3">
                <div class="">
                    <h5 class="font-weight-bolder"><i class="fas fa-edit me-2"></i>Mengubah Tanggal Pemesanan Area Tertentu</h5>
                </div>
                <form action="<?= base_url($role . '/pemesanan/updateRangeAreaTertentu') ?>" method="post">
                    <table id="table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Area</th>
                                <th>Hari Pemesanan</th>
                                <th>Jumlah Tanggal Spandex</th>
                                <th>Jumlah Tanggal Karet</th>
                                <th>Jumlah Tanggal Benang</th>
                                <th>Jumlah Tanggal Nylon</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="area[]" id="" class="form-control">
                                        <option value="">Pilih Area</option>
                                        <?php foreach ($listArea as $area) : ?>
                                            <option value="<?= $area['area'] ?>"><?= $area['area'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="days[]" id="daysAllArea" class="form-select">
                                        <option value="">Pilih Hari</option>
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="range_spandex[]">
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="range_karet[]">
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="range_benang[]">
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="range_nylon[]">
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
                <select name="area[]" id="" class="form-control">
                    <option value="">Pilih Area</option>
                    <?php foreach ($listArea as $area) : ?>
                    <option value="<?= $area['area'] ?>"><?= $area['area'] ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <select name="days[]" id="daysAllArea" class="form-select">
                    <option value="">Pilih Hari</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
            </td>
            <td><input type="number" class="form-control" name="range_spandex[]"></td>
            <td><input type="number" class="form-control" name="range_karet[]"></td>
            <td><input type="number" class="form-control" name="range_benang[]"></td>
            <td><input type="number" class="form-control" name="range_nylon[]"></td>
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

<?php if (session()->getFlashdata('success')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: '<?= session()->getFlashdata('success') ?>',
                confirmButtonColor: '#4a90e2'
            });
        });
    </script>
<?php endif; ?>
<?php if (session()->getFlashdata('error')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: '<?= session()->getFlashdata('error') ?>',
                confirmButtonColor: '#4a90e2'
            });
        });
    </script>
<?php endif; ?>

<?php $this->endSection(); ?>