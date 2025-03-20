<?php $this->extend($role . '/dashboard/header'); ?>
<?php $this->section('content'); ?>

<style>
    .progress {
        height: 20px;
        border-radius: 10px;
        background-color: #f1f1f1;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 10px;
        transition: width 1s ease-in-out;
        font-weight: bold;
        text-align: center;
        line-height: 20px;
    }

    /* Warna progress bar */
    .bg-danger {
        background: linear-gradient(90deg, #ff4d4d, #ff0000);
    }

    .bg-warning {
        background: linear-gradient(90deg, #ffcc00, #ff9900);
    }

    .bg-info {
        background: linear-gradient(90deg, #17a2b8, #007bff);
    }
</style>
<div class="container-fluid py-4">
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
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Status Scheduled</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $scheduled['total_scheduled'] ?? 0 ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Status Rescheduled</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $reschedule['total_reschedule'] ?? 0 ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Status Done</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $done['total_done'] ?? 0 ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-tasks text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Status Retur</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $retur['total_retur'] ?? 0 ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-tasks text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kapasitas Mesin dalam Card -->
    <div class="row">
        <h4 class="mb-3">Kapasitas Mesin</h4>
        <?php foreach ($mesin as $m) :
            $persentase = ($m['kapasitas_terpakai'] / $m['max_caps']) * 100;
            $warna = $persentase > 80 ? 'bg-gradient-danger' : ($persentase > 50 ? 'bg-gradient-warning' : 'bg-gradient-info');
        ?>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <h6 class="font-weight-bold">Mesin <?= $m['no_mesin'] ?></h6>
                        <div class="icon icon-shape <?= $warna ?> shadow text-center border-radius-md mb-2">
                            <i class="fas fa-cogs text-lg opacity-10"></i>
                        </div>
                        <p class="text-muted mb-1">Kapasitas: <?= round($persentase, 2) ?>%</p>
                        <div class="progress">
                            <div class="progress-bar <?= $warna ?>" role="progressbar"
                                style="width: <?= $persentase ?>%; height:100%;"
                                aria-valuenow="<?= $persentase ?>" aria-valuemin="0" aria-valuemax="100">
                                <?= round($persentase, 2) ?>%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php $this->endSection(); ?>