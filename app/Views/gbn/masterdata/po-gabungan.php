<?php $this->extend($role . '/masterdata/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $title; ?></p>
                                <h5 class="font-weight-bolder mb-0">
                                    PO Gabungan
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <button type="submit" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#ListPoModal">
                                <i class="ni ni-single-copy-04 me-2"></i>List PO
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <?php foreach ($jenis as $jn) : ?>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
                <a href="<?= base_url($role . '/masterdata/poGabungan/' . $jn) ?>">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $jn ?></p>
                                        <h5 class="font-weight-bolder mb-0">
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="fas fa-folder text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach ?>
    </div>
</div>

<div class="modal fade" id="ListPoModal" tabindex="-1" aria-labelledby="ListPoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ListPoModalLabel">List Buka PO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url($role . '/listPoGabungan') ?>" method="get" target="_blank">
                    <div class="mb-3">
                        <label for="style_size" class="form-label">Tujuan</label>
                        <select class="form-control tujuan" name="tujuan" required>
                            <option value="-">Pilih Tujuan</option>
                            <option value="CELUP">CELUP</option>
                            <!-- <option value="COVERING">COVERING</option> -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="style_size" class="form-label">Jenis Bahan Baku</label>
                        <select class="form-control jenis" name="jenis" id="jenis" required>
                            <option value="-">Pilih Jenis</option>
                            <option value="BENANG">BENANG</option>
                            <option value="NYLON">NYLON</option>
                            <option value="SPANDEX">SPANDEX & KARET</option>
                        </select>
                        <input type="hidden" class="form-control" id="jenis2" name="jenis2">
                    </div>
                    <!-- Button update dan batal di sebelah kanan -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Ambil elemen select dan input Karet
    const jenisSelect = document.getElementById('jenis');
    const jenis2Input = document.getElementById('jenis2');

    // Tambahkan event listener untuk perubahan pada elemen select
    jenisSelect.addEventListener('change', function() {
        if (jenisSelect.value === 'SPANDEX') {
            jenis2Input.value = 'KARET'; // Jika SPANDEX, isi dengan 'KARET'
        } else {
            jenis2Input.value = ''; // Jika bukan SPANDEX, kosongkan input
        }
    });
</script>
<?php $this->endSection(); ?>