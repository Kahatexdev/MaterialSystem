<?php $this->extend($role . '/mastermaterial/header'); ?>
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

    <!--  -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Form Buka PO</h5>
                <a href="<?= base_url($role . '/material/' . $id_order) ?>" class="btn btn-info"> Kembali</a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card mt-4">
        <div class="card-body">
            <form action="<?= base_url($role . '/openPO/exportPO') ?>" method="post">
                <div class="form-group">
                    <label>Tujuan</label>
                    <select class="form-control" name="tujuan_po" id="selectTujuan" onchange="tujuan()">
                        <option value="-"></option>
                        <option value="Celup Cones">Celup Cones</option>
                        <option value="Covering">Covering</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>No Model</label>
                    <input type="text" class="form-control" name="no_model" value="<?= $model ?>" readonly>
                </div>
                <div id="kebutuhan-container">
                    <label>Pilih Bahan Baku</label>
                    <div class="input-group kebutuhan-item" style="position: relative;">

                        <div class="col-lg-3 mx-2 my-2" style="width: 48%;">
                            <div class="form-group">
                                <label for="itemType">Item Type</label>
                                <select class="form-control item-type" name="items[0][item_type]">
                                    <option value="-">Pilih Item Type</option>
                                    <?php foreach ($order as $type): ?>
                                        <option value="<?= $type['item_type'] ?>"
                                            data-kode-warna="<?= $type['kode_warna'] ?>"
                                            data-color="<?= $type['color'] ?>"
                                            data-kg-mu="<?= $type['kg'] ?>">
                                            <?= $type['item_type'] ?>
                                        </option>
                                        <!-- Tambahkan kg stok dan kg kebutuhan -->
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 mx-2 my-2" style="width: 48%;">
                            <div class="form-group">
                                <label>Kode Warna</label>
                                <input type="text" class="form-control kode-warna" name="items[0][kode_warna]" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3 mx-2" style="width: 48%;">
                            <div class="form-group">
                                <label>Color</label>
                                <input type="text" class="form-control color" name="items[0][color]" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3 mx-2" style="width: 48%;">
                            <div class="form-group">
                                <label for="kgMU">Kg MU</label>
                                <input type="float" class="form-control kg-mu" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3 mx-2" style="width: 48%;">
                            <div class="form-group">
                                <label for="kgStok">Kg Stok</label>
                                <input type="float" class="form-control kg-stok">
                            </div>
                        </div>
                        <div class="col-lg-3 mx-2" style="width: 48%;">
                            <div class="form-group">
                                <label for="kgKebutuhan">Kg Kebutuhan</label>
                                <input type="float" class="form-control kg-kebutuhan" name="items[0][kg_po]">
                            </div>
                        </div>
                        <div style="width: 100%; text-align: center; margin-top: 10px; margin-bottom:10px;">
                            <a style="background: none; border: none; cursor: pointer; font-weight: bold; font-size: 14px;" class="add-more">
                                <img src="<?= base_url('assets/img/add.svg') ?>" style="width: 16px; height: 16px; margin-right: 3px;" alt="add.svg"> Add Item
                            </a>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlInput1">Keterangan</label>
                    <textarea class="form-control" name="keterangan" id="keterangan"></textarea>
                </div>

                <div class="form-group">
                    <label>Penerima</label>
                    <input type="text" class="form-control" id="penerima" readonly>
                </div>
                <div class="form-group">
                    <label>Penanggung Jawab</label>
                    <select class="form-control" name="penanggung_jawab">
                        <option value="-">Pilih</option>
                        <option value="HARTANTO">Hartanto</option>
                        <option value="Megah">Megah</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success w-100">Save</button>
        </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let container = document.getElementById('kebutuhan-container');
        let index = 0;

        // Fungsi untuk menambahkan event listener pada item type baru
        function addItemTypeEventListener(selectElement) {
            selectElement.addEventListener('change', function() {
                let selectedOption = selectElement.options[selectElement.selectedIndex];
                let kodeWarna = selectedOption.getAttribute('data-kode-warna') || '';
                let color = selectedOption.getAttribute('data-color') || '';
                let kgMu = selectedOption.getAttribute('data-kg-mu') || '';

                // Temukan input di baris yang sama dengan select
                let parentGroup = selectElement.closest('.kebutuhan-item');
                parentGroup.querySelector('.kode-warna').value = kodeWarna;
                parentGroup.querySelector('.color').value = color;
                parentGroup.querySelector('.kg-mu').value = kgMu;
            });
        }

        // Tambah elemen baru
        container.addEventListener('click', function(event) {
            if (event.target.closest('.add-more')) {
                index++;
                let newItem = `
                <div class="input-group kebutuhan-item" style="position: relative;">
                    <a class="remove" style="position: absolute; top: 0; right: 0; background: none; border: none; cursor: pointer; padding: 10px;">
                        <img src="<?= base_url('assets/img/cross.svg') ?>" alt="hapus.svg" style="width: 16px; height: 16px;">
                    </a>
                    <div class="col-xl-3 mx-2 my-2" style="width: 48%;">
                        <div class="form-group">
                            <label for="itemType">Item Type</label>
                            <select class="form-control item-type" name="items[${index}][item_type]">
                                <option value="-">Pilih Item Type</option>
                                <?php foreach ($order as $type): ?>
                                    <option value="<?= $type['item_type'] ?>"
                                        data-kode-warna="<?= $type['kode_warna'] ?>"
                                        data-color="<?= $type['color'] ?>"
                                        data-kg-mu="<?= $type['kg'] ?>">
                                        <?= $type['item_type'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 mx-2 my-2" style="width: 48%;">
                        <div class="form-group">
                            <label>Kode Warna</label>
                            <input type="text" class="form-control kode-warna" name="items[${index}][kode_warna]" readonly>
                        </div>
                    </div>
                    <div class="col-xl-3 mx-2" style="width: 48%;">
                        <div class="form-group">
                            <label>Color</label>
                            <input type="text" class="form-control color" name="items[${index}][color]" readonly>
                        </div>
                    </div>
                    <div class="col-xl-3 mx-2" style="width: 48%;">
                        <div class="form-group">
                            <label for="kgMU">Kg MU</label>
                            <input type="float" class="form-control kg-mu" readonly>
                        </div>
                    </div>
                    <div class="col-xl-3 mx-2" style="width: 48%;">
                        <div class="form-group">
                            <label for="kgStok">Kg Stok</label>
                            <input type="float" class="form-control kg-stok">
                        </div>
                    </div>
                    <div class="col-xl-3 mx-2" style="width: 48%;">
                        <div class="form-group">
                            <label for="kgKebutuhan">Kg Kebutuhan</label>
                            <input type="float" class="form-control kg-kebutuhan" name="items[${index}][kg_po]">
                        </div>
                    </div>
                    <div style="width: 100%; text-align: center; margin-top: 10px; margin-bottom:10px;">
                        <a style="background: none; border: none; cursor: pointer; font-weight: bold; font-size: 14px;" class="add-more">
                            <img src="<?= base_url('assets/img/add.svg') ?>" style="width: 16px; height: 16px; margin-right: 3px;" alt="add.svg"> Add Item
                        </a>
                    </div>
                </div>`;
                container.insertAdjacentHTML('beforeend', newItem);

                // Tambahkan event listener ke elemen select yang baru
                let newSelect = container.querySelector(`.kebutuhan-item:last-child .item-type`);
                addItemTypeEventListener(newSelect);
            }
        });

        // Hapus elemen
        container.addEventListener('click', function(event) {
            if (event.target.closest('.remove')) {
                event.target.closest('.kebutuhan-item').remove();
            }
        });

        // Tambahkan event listener ke elemen awal
        document.querySelectorAll('.item-type').forEach(function(selectElement) {
            addItemTypeEventListener(selectElement);
        });
    });


    $(document).ready(function() {
        $('.item-type').on('change', function() {
            // Ambil data dari opsi yang dipilih
            let selectedOption = $(this).find(':selected');
            let kodeWarna = selectedOption.data('kode-warna');
            let kgMU = selectedOption.data('kg-mu');

            // Temukan elemen input terkait
            let container = $(this).closest('.kebutuhan-item');
            container.find('.kode-warna').val(kodeWarna);
            container.find('.kg-mu').val(kgMU);
        });
    });

    function tujuan() {
        let select = document.getElementById('selectTujuan');
        let tujuan = select.value;
        let penerima = document.getElementById('penerima');
        if (tujuan === 'Covering') {
            penerima.value = 'Paryanti';
        } else {
            penerima.value = 'Retno';
        }
    }
</script>


<?php $this->endSection(); ?>