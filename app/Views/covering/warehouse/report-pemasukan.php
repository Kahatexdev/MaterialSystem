<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-muted">Material System</h6>
                <h3 class="mb-0 text-center text-md-start">Report Pemasukan</h3>
            </div>
            <i class="fas fa-chart-line fa-2x text-white p-2 rounded bg-gradient-info"></i>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="pemasukanTable" class="table table-striped table-hover table-bordered text-xs font-bolder" style="width: 100%;">
                    <thead>
                        <tr>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <!-- <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th> -->
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Jenis</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Color</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Code</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">LMD</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">TTL CNS</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">TTL KG</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Box</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">No Rak</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Posisi Rak</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">No Palet</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Admin</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($pemasukan as $row) : ?>
                            <tr>
                                <td class="text-center align-middle"><?= esc($no) ?></td>
                                <!-- <td class="text-center align-middle"><?= esc($row['no_model']) ?></td> -->
                                <td class="text-center align-middle"><?= esc($row['jenis']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['color']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['code']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['lmd']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['ttl_cns']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['ttl_kg']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['box']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['no_rak']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['posisi_rak']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['no_palet']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['admin']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['keterangan']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['created_at']) ?></td>
                            </tr>
                            <?php $no++ ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#pemasukanTable').DataTable({});
    });
</script>


<?php $this->endSection(); ?>