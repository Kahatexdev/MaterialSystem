<?php $this->extend($role . '/dashboard/header'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Covering System</p>
                                <h5 class="font-weight-bolder mb-0">Dashboard</h5>
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
        <?php
        $stats = [
            ['title' => 'Total PO', 'icon' => 'fas fa-book', 'value' => $poCount],
            ['title' => 'Total Qty PO', 'icon' => 'ni ni-cart', 'value' => number_format($ttlQtyPO, 2, ',', '.') . ' KG'],
            ['title' => 'Pemasukan/Hari', 'icon' => 'fas fa-arrow-down', 'value' => number_format($incomeToday, 2, ',', '.') . ' KG'],
            ['title' => 'Pengeluaran/Hari', 'icon' => 'fas fa-arrow-up', 'value' => number_format($expenseToday, 2, ',', '.') . ' KG']
        ];
        ?>

        <?php foreach ($stats as $stat) : ?>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $stat['title'] ?></p>
                                    <h5 class="font-weight-bolder mb-0">
                                        <?= $stat['value'] ?>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="<?= $stat['icon'] ?> text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Grafik Pemasukan & Pengeluaran -->
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="font-weight-bold text-uppercase">Statistik Pemasukan dan Pengeluaran 7 Hari Terakhir</h6>
                    <canvas id="financeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = <?= json_encode($dateLabels) ?>;
    const dataIn = <?= json_encode($incomeData) ?>;
    const dataOut = <?= json_encode($expenseData) ?>;
    const detailData = <?= json_encode($detailData) ?>;

    const ctx = document.getElementById('financeChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                    label: 'Pemasukan',
                    data: dataIn,
                    backgroundColor: 'rgba(33,179,253,0.6)',
                    borderColor: 'rgba(33,179,253,1)',
                    borderWidth: 1,
                    stack: 'combined'
                },
                {
                    label: 'Pengeluaran',
                    data: dataOut,
                    backgroundColor: 'rgba(255,99,132,0.6)',
                    borderColor: 'rgba(255,99,132,1)',
                    borderWidth: 1,
                    stack: 'combined'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        // Judul tooltip: tanggal
                        title: (items) => items[0].label,
                        // Baris pertama: ringkasan sum
                        label: (item) => {
                            const val = item.parsed.y;
                            return item.dataset.label + ': ' + val.toFixed(2) + ' kg';
                        },
                        // Tambah detail di bawahnya
                        afterBody: (items) => {
                            const idx = items[0].dataIndex;
                            const datasetLabel = items[0].dataset.label.toLowerCase(); // 'pemasukan' atau 'pengeluaran'
                            const isIncome = datasetLabel.includes('pemasukan');
                            const details = (detailData[idx] || []).filter(d =>
                                (isIncome && d.type === 'in') || (!isIncome && d.type === 'out')
                            );

                            if (!details.length) return ['(no detail)'];

                            // satu array string per baris
                            return details.map(d => {
                                const sign = (d.type === 'out' ? '-' : '+');
                                return `${sign} ${d.jenis} (${d.color}, ${d.code}): ${d.ttl_kg}kg`;
                            });
                        }
                    }
                }
            }
        }
    });
</script>


<?php $this->endSection(); ?>