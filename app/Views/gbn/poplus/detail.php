<?php $this->extend($role . '/poplus/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
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
                });
            });
        </script>
    <?php endif; ?>

    <!-- Header Card -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <h5 class="mb-0 font-weight-bolder">Detail Po Tambahan Tanggal <?= $tglPo ?></h5>
                    <h4><?= $noModel ?> || <?= $itemType ?> || <?= $kodeWarna ?> || <?= $warna ?></h4>
                </div>
                <div class="col-6 text-end">
                    <a href="<?= base_url($role . '/poplus') ?>" class="btn bg-gradient-info"> Kembali</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Style Size</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">PO(+) Kg</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detail as $data):
                            $no = 1;
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $data['style_size'] ?></td>
                                <td><?= number_format($data['poplus_mc_kg'] + $data['plus_pck_kg'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($detail)) : ?>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <p>No data available in the table.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "pageLength": 35,
            "order": []
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.btn-approve');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modal-id').value = this.getAttribute('data-id');
                document.getElementById('modal-tgl').value = this.getAttribute('data-tgl');
                document.getElementById('modal-model').value = this.getAttribute('data-model');
                document.getElementById('modal-type').value = this.getAttribute('data-type');
                document.getElementById('modal-warna').value = this.getAttribute('data-warna');
                document.getElementById('modal-status').value = this.getAttribute('data-status');
                document.getElementById('modal-area').value = this.getAttribute('data-area');
            });
        });
    });
</script>
<?php $this->endSection(); ?>