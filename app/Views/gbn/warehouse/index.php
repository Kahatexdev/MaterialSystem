<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<style>

</style>

<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?><script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
                });
            });
        </script><?php endif; ?><?php if (session()->getFlashdata('error')) : ?><script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5>Stock Material </h5>
                    </div>
                    <form class="form" method="post" action="<?= base_url('gbn/warehouse') ?>">
                        <div class="row">
                            <div class="col-lg-5 col-sm-12">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="noModel" placeholder="Masukkan No Model" id="">
                                </div>
                            </div>
                            <div class="col-lg-5 col-sm-12">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="warna" placeholder="Masukkan Kode Warna" id="">
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-info" id="filter_date_range">Filter </button>
                                    <button class="btn btn-warning" id="reset_date_range">Reset </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class=" card my-1">
        <div class="card-body">
            <div class="row">
                <div class="d-flex justify-content-between">
                    <h6>$cluster </h6><button class="btn btn-danger">Hapus </button>
                </div>
            </div>
            <div class="row"><strong>No Model : $noModel </strong>
                <div class="col-lg-3 col-md-4 col-sm-12">
                    <div class="row"><label for="" class="form-control-label">Kode Follup : $follup </label></div>
                    <div class="row"><label for="" class="form-control-label">Space : $space </label></div>
                    <div class="row"><label for="" class="form-control-label">Sisa : $Sisa </label></div>
                    <div class="row"><label for="" class="form-control-label">Buyer : $buyer </label></div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-12">
                    <div class="row"><label for="" class="form-control-label">Kode Benang : $kdbn </label></div>
                    <div class="row"><label for="" class="form-control-label">Lot Jalur : $lot </label></div>
                    <div class="row"><label for="" class="form-control-label">Delivery Awal : 0000-00-00 </label></div>
                    <div class="row"><label for="" class="form-control-label">Delivery Akhir : $akihr </label></div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-12">
                    <div class="row"><label for="" class="form-control-label">Kode Warna : $kdwarna </label></div>
                    <div class="row"><label for="" class="form-control-label">Warna : $Warna </label></div>
                    <div class="row"><label for="" class="form-control-label">Total KG/s : $totalkg </label></div>
                    <div class="row"><label for="" class="form-control-label">Total KRG: $totalKrg </label></div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-12">
                    <div class="row"><label for="" class="form-control-label">Keterangan : $kdwarna </label></div>
                    <div class="row"><button class="btn btn-sm btn-info">In/Out</button></div>
                    <div class="row"><button class="btn btn-sm btn-info">Pindah Palet</button></div>
                    <div class="row"><button class="btn btn-sm btn-info ">Pindah Order</button></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>