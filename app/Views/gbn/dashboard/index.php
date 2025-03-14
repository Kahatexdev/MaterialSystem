<?php $this->extend($role . '/dashboard/header'); ?>
<?php $this->section('content'); ?>

<style>
    .cell {
        border: none;
        padding: 8px 12px;
        margin: 2px;
        border-radius: 8px;
        /* Membuat tombol rounded */
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    /* Warna cell */
    .gray-cell {
        background-color: #b0b0b0;
        color: white;
    }

    .blue-cell {
        background-color: #007bff;
        color: white;
    }

    .orange-cell {
        background-color: #ff851b;
        color: white;
    }

    .red-cell {
        background-color: #dc3545;
        color: white;
    }

    /* Hover effect */
    .cell:hover {
        opacity: 0.8;
    }

    /* Styling table */
    .table-bordered th,
    .table-bordered td {
        border: 2px solid #dee2e6;
        text-align: center;
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">PDK</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $pdk['total_pdk'] ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-book text-lg opacity-10" aria-hidden="true"></i>
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
                                    <?= $schedule['total_done'] ?>
                                    <!-- Sesuaikan dengan last status sent dll. -->
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Pemasukan/Hari</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $pemasukan['total_karung_masuk'] ?> Karung
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-settings text-lg opacity-10" aria-hidden="true"></i>
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Pengeluaran/Hari</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $pengeluaran['total_karung_keluar'] ?> Karung
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="mt-3 ms-3">
                    <h4><strong>LAYOUT STOCK ORDER</strong></h4>
                </div>
                <div class="card-body">
                    <!-- Keterangan kapasitas -->
                    <div class="mb-2 text-center">
                        <button class="btn text-white" style="background-color: #b0b0b0;">0% (Gray)</button>
                        <button class="btn text-white" style="background-color: #007bff;">1-70% (Blue)</button>
                        <button class="btn text-white" style="background-color: #ff851b;">71-99% (Orange)</button>
                        <button class="btn text-white" style="background-color: #dc3545;">100% (Red)</button>
                    </div>
                    <form>
                        <div class="mb-3">
                            <label for="groupSelect" class="form-label">Select Group</label>
                            <select class="form-select" id="groupSelect">
                                <option selected>Group I</option>
                                <option>Group II</option>
                                <option>Group III</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info w-100">Apply</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 mt-3">
        <div class="col">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h3 style="color:rgb(255, 255, 255);" class="mb-0 text-center">GROUP I</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <?php for ($i = 'A'; $i <= 'L'; $i++): ?>
                                        <th class="header-cell"><?= $i ?></th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($row = 9; $row >= 1; $row--): ?>
                                    <tr>
                                        <?php for ($col = 'A'; $col <= 'L'; $col++): ?>
                                            <td class="p-1">
                                                <div class="d-flex justify-content-center">
                                                    <?php
                                                    // Menentukan warna cell secara acak termasuk merah
                                                    $colors = ['gray-cell', 'blue-cell', 'orange-cell'];
                                                    $aClass = $colors[rand(0, 2)];
                                                    $bClass = $colors[rand(0, 2)];
                                                    ?>
                                                    <button class="cell <?= $aClass ?>"><?= $row ?>a</button>
                                                    <button class="cell <?= $bClass ?>"><?= $row ?>b</button>
                                                </div>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
    // Initialize chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('statsChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'],
                datasets: [{
                    label: 'Distribution',
                    data: [12, 19, 3, 5, 2, 3, 7, 8, 9, 10, 11, 6],
                    backgroundColor: [
                        'rgba(51, 122, 183, 0.7)',
                    ],
                    borderColor: [
                        'rgba(51, 122, 183, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
<?php $this->endSection(); ?>