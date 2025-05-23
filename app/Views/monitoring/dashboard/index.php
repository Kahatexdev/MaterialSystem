<?php $this->extend($role . '/dashboard/header'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Dashboard
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-dark shadow-info text-center rounded-circle">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="mb-0 text-capitalize font-weight-bold" style="font-size: 12px;">Pemesanan Belum Dikirim</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= esc($pemesanan[0]['COUNT(id_pemesanan)'] ?? 0) ?>
                                </h5>
                                <p class="mb-0 text-sm text-dark font-weight-bolder">Hari Ini</p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-dark shadow-dark text-center rounded-circle">
                                <i class="ni ni-delivery-fast text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Sch Completed</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= esc($schedule['total_done'] ?? 0) ?>
                                </h5>
                                <p class="mb-0 text-sm text-dark font-weight-bolder">Hari Ini</p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-dark shadow-dark text-center rounded-circle">
                                <i class="fas fa-tasks text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Pemasukan/Hari</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $pemasukan['total_karung_masuk'] ?? 0 ?> Karung
                                </h5>
                                <p class="mb-0 text-sm text-dark font-weight-bolder">Hari Ini</p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-dark shadow-dark text-center rounded-circle">
                                <i class="ni ni-bold-down text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Pengeluaran/Hari</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $pengeluaran['total_karung_keluar'] ?? 0 ?> Karung
                                </h5>
                                <p class="mb-0 text-sm text-dark font-weight-bolder">Hari Ini</p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-dark shadow-dark text-center rounded-circle">
                                <i class="ni ni-bold-up text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- TABEL JADWAL TERDEKAT -->
    <div class="col-xl-12 mt-4">
        <div class="card overflow-hidden">
            <div class="card-header p-3 bg-gradient-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bolder">Jadwal Celup Terdekat (5 Hari ke Depan)</h6>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No Mesin</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">No Model</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Item Type</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Kode Warna</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Warna</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Tgl Schedule</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($schTerdekat)) : ?>
                                <?php foreach ($schTerdekat as $row) : ?>
                                    <tr class="border-bottom">
                                        <td class="ps-3 py-3">
                                            <span class="text-sm font-weight-bold"><?= esc($row['no_mesin']) ?></span>
                                        </td>
                                        <td class="ps-3 py-3">
                                            <span class="text-sm font-weight-bold"><?= esc($row['no_model']) ?></span>
                                        </td>
                                        <td class="ps-3 py-3">
                                            <span class="text-sm font-weight-normal"><?= esc($row['item_type']) ?></span>
                                        </td>
                                        <td class="ps-3 py-3">
                                            <span class="badge bg-light text-dark px-3 py-2"><?= esc($row['kode_warna']) ?></span>
                                        </td>
                                        <td class="ps-3 py-3">
                                            <span class="text-sm font-weight-normal"><?= esc($row['warna']) ?></span>
                                        </td>
                                        <td class="ps-3 py-3">
                                            <span class="text-sm font-weight-bold text-info"><?= date('d-m-Y', strtotime($row['tanggal_schedule'])) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center p-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ni ni-calendar-grid-58 text-muted mb-2" style="font-size: 2rem;"></i>
                                            <span class="text-sm font-weight-normal text-muted">Tidak ada jadwal dalam 5 hari ke depan.</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END TABEL -->


</div>


<?php $this->endSection(); ?>