<?php $this->extend($role . '/mastermaterial/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<style>
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.2;
        border-radius: 0.5rem;
        width: 40px;
        height: 32px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .btn-xs i {
        font-size: 0.8rem;
    }
</style>
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
                <div class="text-header">

                    <h5 class="mb-0 font-weight-bolder">Data Material <?= $no_model ?></h5>
                </div>
                <div class="group">
                    <button id="btn-delete-selected" class="btn btn-danger me-2">
                        <i class="fas fa-trash"></i> Hapus Terpilih
                    </button>
                    <button type="button" class="btn btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#ubahAreaModal">
                        <i class="ni ni-building me-2"></i>Ubah Area
                    </button>
                    <button type="button" class="btn btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#tambahModal">
                        <i class="fas fa-plus me-2"></i>Material
                    </button>
                    <a href="<?= base_url($role . '/openPO/' . $id_order) ?>" class="btn btn-outline-info me-2">
                        <i class="fas fa-file-import me-2"></i>Buka PO
                    </a>
                    <button type="submit" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#ListPoModal">
                        <i class="ni ni-single-copy-04 me-2"></i>List PO
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="display text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center"></th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Style Size</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Inisial</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Color</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Composition (%)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">GW</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty(pcs)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Loss</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kgs</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderData as $data): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check delete-checkbox" data-id="<?= $data['id_material'] ?>">
                                </td>
                                <td><?= $data['style_size'] ?></td>
                                <td><?= $data['area'] ?></td>
                                <td><?= $data['inisial'] ?></td>
                                <td><?= $data['item_type'] ?></td>
                                <td><?= $data['kode_warna'] ?></td>
                                <td><?= $data['color'] ?></td>
                                <td><?= ($data['composition'] == 1) ? '-' : $data['composition'] . ' %' ?></td>
                                <td><?= ($data['gw'] == 1) ? '-' : $data['gw'] ?></td>
                                <td><?= ($data['qty_pcs'] == 1) ? '-' : $data['qty_pcs'] ?></td>
                                <td><?= ($data['loss'] == 1) ? '-' : $data['loss'] ?></td>
                                <td><?= $data['kgs'] ?></td>
                                <td><?= $data['keterangan'] ?></td>
                                <td class="d-flex justify-content-start align-items-center gap-1">
                                    <button class="btn btn-warning btn-xs btn-edit" data-id="<?= $data['id_material'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-xs btn-delete" data-id="<?= $data['id_material'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-info btn-xs btn-split" data-id="<?= $data['id_material'] ?>" id="btn-split">
                                        <i class="fas fa-grip-lines-vertical"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($orderData)) : ?>
                <div class=" card-footer">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <p>No data available in the table.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Modal Tambah Data -->
        <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahModalLabel">Tambah Data Material</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/tambahMaterial') ?>" method="post" id="form-material">
                            <input type="hidden" name="id_order" value="<?= $id_order ?>">
                            <label for="st" class="form-label">Style:</label>
                            <div class="d-flex flex-wrap gap-3">
                                <?php foreach ($style as $st): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="style_size[]" value="<?= $st['style_size'] ?>" id="style_<?= $st['style_size'] ?>">
                                        <label class="form-check-label" for="style_<?= $st['style_size'] ?>">
                                            <?= esc($st['style_size']) ?> | (<?= esc($st['inisial'] ?? '-') ?>)
                                        </label>
                                    </div>
                                    <input type="hidden" name="inisial[]" id="" value="<?= $st['inisial'] ?>">
                                <?php endforeach; ?>
                            </div>

                            <div class="mb-3">
                                <label for="area" class="form-label">Area</label>
                                <select class="form-control" name="area" id="add_area">
                                    <option value="">Pilih Area</option>
                                    <option value="KK1A">KK1A</option>
                                    <option value="KK1B">KK1B</option>
                                    <option value="KK2A">KK2A</option>
                                    <option value="KK2B">KK2B</option>
                                    <option value="KK2C">KK2C</option>
                                    <option value="KK5G">KK5G</option>
                                    <option value="KK7K">KK7K</option>
                                    <option value="KK7L">KK7L</option>
                                    <option value="KK8D">KK8D</option>
                                    <option value="KK8F">KK8F</option>
                                    <option value="KK8J">KK8J</option>
                                    <option value="KK9">KK9</option>
                                    <option value="KK10">KK10</option>
                                    <option value="KK11M">KK11M</option>
                                    <option value="Belum Ada Area">Belum Ada Area</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="itemType">Item Type</label>
                                <select class="" id="add_item_type" name="item_type">
                                    <option value="">Pilih Item Type</option>
                                    <?php foreach ($itemType as $item): ?>
                                        <option value="<?= $item['item_type'] ?>"><?= $item['item_type'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row mb-3">

                                <div class="col-6">
                                    <label for="kode_warna" class="form-label">Kode Warna</label>
                                    <input type="text" class="form-control" id="add_kode_warna" name="kode_warna" required>
                                </div>

                                <div class=" col-6">
                                    <label for="color" class="form-label">Color</label>
                                    <input type="text" class="form-control" id="add_color" name="color" required>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" checked="">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Gabungan</label>
                            </div>
                            <div class="row" id="prop">

                                <div class="col-3">
                                    <label for="composition" class="form-label">Composition (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="add_composition" name="composition" required>
                                </div>

                                <div class="col-3">
                                    <label for="gw" class="form-label">GW</label>
                                    <input type="number" step="0.01" class="form-control" id="add_gw" name="gw" required>
                                </div>

                                <div class="col-3">
                                    <label for="qty_pcs" class="form-label">Qty(pcs)</label>
                                    <input type="number" step="0.01" class="form-control" id="add_qty_pcs" name="qty_pcs" required>
                                </div>

                                <div class="col-3">
                                    <label for="qty_pcs" class="form-label">Loss</label>
                                    <input type="number" step="0.01" class="form-control" id="add_loss" name="loss" required>
                                </div>

                                <div class="col-3">
                                    <label for="qty_pcs" class="form-label">Kgs</label>
                                    <input type="number" step="0.01" class="form-control" id="add_kgs" name="kgs" required>
                                </div>

                                <div class="col-3">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <textarea name="keterangan" id="add_keterangan" class="form-control" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-info">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit Data Material -->
        <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Update Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <form id="updateForm" action="<?= base_url($role . '/updateMaterial') ?>" method="post">
                            <input type="hidden" name="id_material" id="id_material">
                            <input type="hidden" name="id_order" id="id_order">
                            <div class="mb-3">
                                <label for="style_size" class="form-label">Style Size</label>
                                <input type="text" class="form-control" id="style_size" name="style_size" required>
                            </div>

                            <div class="mb-3">
                                <label for="area" class="form-label">Area</label>
                                <input type="text" class="form-control" id="area" name="area" required>
                            </div>

                            <div class="mb-3">
                                <label for="inisial" class="form-label">Inisial</label>
                                <input type="text" class="form-control" id="inisial" name="inisial">
                            </div>

                            <div class="mb-3">
                                <label for="color" class="form-label">Color</label>
                                <input type="text" class="form-control" id="color" name="color" required>
                            </div>

                            <div class="mb-3">
                                <label for="item_type" class="form-label">Item Type</label>
                                <input type="text" class="form-control" id="item_type" name="item_type" required>
                            </div>

                            <div class="mb-3">
                                <label for="kode_warna" class="form-label">Kode Warna</label>
                                <input type="text" class="form-control" id="kode_warna" name="kode_warna" required>
                            </div>

                            <div class="mb-3">
                                <label for="composition" class="form-label">Composition (%)</label>
                                <input type="number" step="0.01" class="form-control" id="composition" name="composition" required>
                            </div>

                            <div class="mb-3">
                                <label for="gw" class="form-label">GW</label>
                                <input type="number" step="0.01" class="form-control" id="gw" name="gw" required>
                            </div>

                            <div class="mb-3">
                                <label for="qty_pcs" class="form-label">Qty(pcs)</label>
                                <input type="number" step="0.01" class="form-control" id="qty_pcs" name="qty_pcs" required>
                            </div>

                            <div class="mb-3">
                                <label for="loss" class="form-label">Loss</label>
                                <input type="number" step="0.01" class="form-control" id="loss" name="loss" required>
                            </div>

                            <div class="mb-3">
                                <label for="kgs" class="form-label">Kgs</label>
                                <input type="number" step="0.01" class="form-control" id="kgs" name="kgs" required>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="2"></textarea>
                            </div>
                            <!-- Button update dan batal di sebelah kanan -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-info">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
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
                        <form action="<?= base_url($role . '/listOpenPO/' . $no_model) ?>" method="get" target="_blank">
                            <div class="mb-3">
                                <label for="style_size" class="form-label">Tujuan</label>
                                <select class="form-control tujuan" name="tujuan" required>
                                    <option value="">Pilih Tujuan</option>
                                    <option value="CELUP">CELUP</option>
                                    <option value="COVERING">COVERING</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="style_size" class="form-label">Jenis Bahan Baku</label>
                                <select class="form-control jenis" name="jenis" id="jenis" required>
                                    <option value="">Pilih Jenis</option>
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
    </div>
    <div class="card mt-4" id="totalKebutuhan">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Total Kebutuhan Bahan Baku</h5>
                <a href="<?= base_url($role . '/material/exportTotalKebutuhan/' . $id_order) ?>" class="btn btn-success">
                    <i class="fas fa-file-excel me-2"></i>Excel
                </a>
            </div>
            <div class="table-responsive">
                <table id="dataTable1" class="display text-uppercase text-xs font-bolder text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Color</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Kebutuhan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Sch</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kebutuhan as $data1): ?>
                            <tr>
                                <td><?= $data1['item_type'] ?></td>
                                <td><?= $data1['kode_warna'] ?></td>
                                <td><?= $data1['color'] ?></td>
                                <td><?= number_format($data1['kebutuhan'], 2) ?> kg</td>
                                <td><?= $data1['tanggal_schedule'] ?></td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($kebutuhan)) : ?>
                <div class=" card-footer">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <p>No data available in the table.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Ubah Semua Area -->
    <div class="modal fade" id="ubahAreaModal" tabindex="-1" aria-labelledby="ubahAreaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ubahAreaModalLabel">Ubah Area</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url($role . '/updateArea/' . $id_order) ?>" method="post">
                        <input type="hidden" name="id_order" value="<?= $id_order ?>">
                        <div class="mb-3">
                            <label for="area" class="form-label">Area</label>
                            <select class="form-control" name="edit_all_area" id="edit_all_area">
                                <option value="">Pilih Area</option>
                                <?php
                                $areaList = ['KK1A', 'KK1B', 'KK2A', 'KK2B', 'KK2C', 'KK5G', 'KK7K', 'KK7L', 'KK8D', 'KK8F', 'KK8J', 'KK9', 'KK10', 'KK11M', 'Gedung 1', 'Gedung 2', 'MJ'];

                                foreach ($areaList as $areaOption) {
                                    $selected = in_array($areaOption, $area) ? 'selected' : ''; // Cek apakah area ada di $areaData
                                ?>
                                    <option value="<?= $areaOption ?>" <?= $selected ?>><?= $areaOption ?></option>
                                <?php } ?>
                            </select>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-info">Ubah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Split Qty -->
    <div class="modal fade" id="modalSplitMaterial" tabindex="-1" aria-labelledby="modalSplitLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="splitQtyModalLabel">Split Qty</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <form id="splitForm" action="<?= base_url($role . '/splitMaterial') ?>" method="post">
                        <input type="hidden" name="id_material" id="id_material_split">
                        <input type="hidden" name="id_order" id="id_order_split">
                        <input type="hidden" class="form-control" id="color_split" name="color" readonly>
                        <input type="hidden" class="form-control" id="area_split" name="area" readonly>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="style_size" class="form-label">Style Size</label>
                                    <input type="text" class="form-control" id="style_size_split" name="style_size" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="inisial" class="form-label">Inisial</label>
                                    <input type="text" class="form-control" id="inisial_split" name="inisial" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="item_type" class="form-label">Item Type</label>
                                    <input type="text" class="form-control" id="item_type_split" name="item_type" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="item_type" class="form-label">Kode Warna</label>
                                    <input type="text" class="form-control" id="kode_warna_split" name="kode_warna" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gw" class="form-label">GW</label>
                                    <input type="text" class="form-control" id="gw_split" name="gw" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="composition" class="form-label">Composition</label>
                                    <input type="text" class="form-control" id="composition_split" name="composition" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="loss" class="form-label">Loss</label>
                                    <input type="number" class="form-control" id="loss_split" name="loss" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="area" class="form-label">Kgs Awal</label>
                                    <input type="number" class="form-control" id="kgs_split" name="kgs_awal" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="item_type" class="form-label">Qty Awal</label>
                                    <input type="number" class="form-control" id="qty_pcs_split" name="qty_pcs" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="area" class="form-label">Item Type 1</label>
                                    <input type="text" class="form-control" id="item_type_split1" name="item_type" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="inisial" class="form-label">Percentage 1</label>
                                    <input type="number" step="0.01" class="form-control" id="percentage1" name="percentage1" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Item Type 2</label>
                                    <select name="item_type_split_2" id="item_type_split_2" class="" required>
                                        <option value="">Pilih Item Type</option>
                                        <?php foreach ($itemType as $item): ?>
                                            <option value="<?= $item['item_type'] ?>"><?= $item['item_type'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="item_type" class="form-label">Percentage 2</label>
                                    <input type="number" step="0.01" class="form-control" id="percentage2" name="percentage2" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="kgs1" class="form-label">Kgs Item Type 1</label>
                                    <input type="number" step="0.01" class="form-control" id="kgs1" name="kgs_1" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="kgs1" class="form-label">Composition 1</label>
                                    <input type="number" step="0.01" class="form-control" id="comp1" name="comp1" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="kgs2" class="form-label">Kgs Item Type 2</label>
                                    <input type="number" step="0.01" class="form-control" id="kgs2" name="kgs_2" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="kgs1" class="form-label">Composition 2</label>
                                    <input type="number" step="0.01" class="form-control" id="comp2" name="comp2" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-info">Split</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#tambahModal').on('shown.bs.modal', function() {
            $('#add_item_type').select2({
                dropdownParent: $('#tambahModal'),
                width: '100%',
            });
        });
    });

    $(document).ready(function() {
        $('#dataTable').DataTable({
            "pageLength": 35,
            "order": []
        });
        $('#dataTable1').DataTable({
            "pageLength": 35,
            "order": []
        });
        // Event listener tombol Update
        $('#dataTable').on('click', '.btn-edit', function() {
            const id = $(this).data('id');

            // Lakukan AJAX request untuk mendapatkan data
            $.ajax({
                url: '<?= base_url($role . '/getMaterialDetails') ?>/' + id,
                type: 'GET',
                success: function(response) {
                    // Isi data ke dalam form modal
                    $('#id_material').val(response.id_material);
                    $('#id_order').val(response.id_order);
                    $('#style_size').val(response.style_size);
                    $('#area').val(response.area);
                    $('#inisial').val(response.inisial);
                    $('#color').val(response.color);
                    $('#item_type').val(response.item_type);
                    $('#kode_warna').val(response.kode_warna);
                    $('#composition').val(response.composition);
                    $('#gw').val(response.gw);
                    $('#qty_pcs').val(response.qty_pcs);
                    $('#loss').val(response.loss);
                    $('#kgs').val(response.kgs);
                    $('#keterangan').val(response.keterangan);
                    // Show modal dialog
                    $('#updateModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Event listener tombol Delete
        $('#dataTable').on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            const idOrder = <?= $id_order ?>;
            console.log(idOrder, id);
            // Tampilkan konfirmasi dialog
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect ke link hapus
                    window.location = '<?= base_url($role . '/deleteMaterial') ?>/' + id + '/' + idOrder;
                }
            });
        });

    });

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
<script>
    $(document).ready(function() {

        // Fungsi hitung otomatis Kgs berdasarkan persentase dan Kgs Awal
        function hitungKgsSplit() {
            const kgsAwal = parseFloat($('#kgs_split').val()) || 0;
            const compAwal = parseFloat($('#composition_split').val()) || 0;
            const perc1 = parseFloat($('#percentage1').val()) || 0;
            const perc2 = parseFloat($('#percentage2').val()) || 0;

            // Validasi total persentase
            if (perc1 + perc2 > 100) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Total persentase tidak boleh lebih dari 100%',
                });
                // reset hasil
                $('#kgs1, #kgs2, #comp1, #comp2').val('');
                return;
            }

            // Hitung langsung dari Kgs Awal
            const kgs1 = kgsAwal * (perc1 / 100);
            const kgs2 = kgsAwal * (perc2 / 100);

            //Hitung komposisi baru
            const newComp1 = compAwal * (perc1 / 100);
            const newComp2 = compAwal * (perc2 / 100);

            // Tampilkan hasil
            $('#kgs1').val(kgs1.toFixed(2));
            $('#kgs2').val(kgs2.toFixed(2));
            $('#comp1').val(newComp1.toFixed(2));
            $('#comp2').val(newComp2.toFixed(2));
        }

        // Saat tombol split diklik: fetch data + tampilkan modal
        $(document).on('click', '.btn-split', function() {
            $('#modalSplitMaterial').on('shown.bs.modal', function() {
                $('#item_type_split_2').select2({
                    dropdownParent: $('#modalSplitMaterial'),
                    // theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Pilih Item Type',
                    allowClear: true
                });
            });

            const materialId = $(this).data('id');
            $('#percentage1, #percentage2, #kgs1, #kgs2, #comp1, #comp2').val('');

            $.ajax({
                url: '<?= base_url($role . '/getMaterialDetails') ?>/' + materialId,
                type: 'GET',
                success: function(res) {
                    // isi semua field seperti sebelumnya...
                    $('#id_material_split').val(res.id_material);
                    $('#id_order_split').val(res.id_order);
                    $('#style_size_split').val(res.style_size);
                    $('#inisial_split').val(res.inisial);
                    $('#gw_split').val(res.gw);
                    $('#loss_split').val(res.loss);
                    $('#item_type_split').val(res.item_type);
                    $('#item_type_split1').val(res.item_type);
                    $('#qty_pcs_split').val(res.qty_pcs);
                    $('#area_split').val(res.area);
                    $('#composition_split').val(res.composition);
                    $('#kode_warna_split').val(res.kode_warna);
                    $('#color_split').val(res.color);
                    $('#kgs_split').val(res.kgs);

                    // reset persentase & hasil
                    $('#percentage1, #percentage2').val('');
                    $('#kgs1, #kgs2').val('');

                    // buka modal
                    $('#modalSplitMaterial').modal('show');
                },
                error: function() {
                    alert('Gagal mengambil data material.');
                }
            });
        });

        // Re-hitung setiap user ubah percentage
        $('#percentage1, #percentage2').on('input', hitungKgsSplit);

        // Validasi & kirim data via AJAX
        $('#splitForm').on('submit', function(e) {
            e.preventDefault();

            const perc1 = parseFloat($('#percentage1').val()) || 0;
            const perc2 = parseFloat($('#percentage2').val()) || 0;
            const comp1 = parseFloat($('#comp1').val()) || 0;
            const comp2 = parseFloat($('#comp2').val()) || 0;

            if (perc1 + perc2 !== 100) {
                alert('Total persentase harus 100%');
                return;
            }

            $.ajax({
                url: '<?= base_url($role . '/splitMaterial') ?>',
                type: 'POST',
                data: {
                    id_material_old: $('#id_material_split').val(),
                    id_order: $('#id_order_split').val(),
                    style_size: $('#style_size_split').val(),
                    inisial: $('#inisial_split').val(),
                    gw: $('#gw_split').val(),
                    loss: $('#loss_split').val(),
                    item_type_1: $('#item_type_split1').val(),
                    item_type_2: $('#item_type_split_2').val(),
                    composition: $('#composition_split').val(),
                    kode_warna: $('#kode_warna_split').val(),
                    color: $('#color_split').val(),
                    area: $('#area_split').val(),
                    qty_pcs: $('#qty_pcs_split').val(),
                    percentage1: perc1,
                    percentage2: perc2,
                    comp1: comp1,
                    comp2: comp2,
                    kgs_1: $('#kgs1').val(),
                    kgs_2: $('#kgs2').val()
                },
                success() {
                    Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Material berhasil di‑split!'
                        })
                        .then(() => location.reload());
                },
                error() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat split data.'
                    });
                }
            });
        });

    });
</script>
<script>
    document.getElementById('btn-delete-selected').addEventListener('click', function() {
        const selected = Array.from(document.querySelectorAll('.delete-checkbox:checked'))
            .map(cb => cb.getAttribute('data-id'));

        if (selected.length === 0) {
            Swal.fire('Peringatan', 'Tidak ada data yang dipilih.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: `Menghapus ${selected.length} data terpilih.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url($role . '/material/deleteSelected') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            ids: selected
                        })
                    })
                    .then(res => res.json())
                    .then(response => {
                        Swal.fire('Berhasil', response.message, 'success').then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire('Error', 'Terjadi kesalahan saat menghapus.', 'error');
                    });
            }
        });
    });
</script>

<!-- <script>
    $(document).ready(function() {
        function hitungKgsSplit() {
            let gw = parseFloat($('#gw_split').val()) || 0;
            let comp = parseFloat($('#composition_split').val()) || 0; // Misal 85.95
            let loss = parseFloat($('#loss_split').val()) || 0; // Misal 5
            let qtyPcs = parseFloat($('#qty_pcs_split').val()) || 0;
            let qty1 = parseFloat($('#qty1').val()) || 0;
            let qty2 = parseFloat($('#qty2').val()) || 0;

            // Hitung total Komposisi + Loss dengan konsep persen yang benar
            let totalCompLoss = comp / 100 * (1 + loss / 100); // Menambahkan loss sebagai persen dari comp
            let totalKgs = ((gw * totalCompLoss) * qtyPcs) / 1000;

            // Bagi sesuai Qty Area 1 dan Area 2
            let kgsArea1 = (totalKgs * qty1) / qtyPcs;
            let kgsArea2 = (totalKgs * qty2) / qtyPcs;

            $('#kgs1').val(kgsArea1.toFixed(2)); // Menampilkan hasil dengan 2 angka desimal
            $('#kgs2').val(kgsArea2.toFixed(2)); // Menampilkan hasil dengan 2 angka desimal
        }

        // Panggil fungsi hitung otomatis saat modal dibuka
        $(document).on('click', '.btn-split', function() {
            let materialId = $(this).data('id');

            $.ajax({
                url: '<?= base_url($role . '/getMaterialDetails') ?>/' + materialId,
                type: 'GET',
                success: function(response) {
                    $('#id_material_split').val(response.id_material);
                    $('#id_order_split').val(response.id_order);
                    $('#style_size_split').val(response.style_size);
                    $('#inisial_split').val(response.inisial);
                    $('#gw_split').val(response.gw);
                    $('#loss_split').val(response.loss);
                    $('#item_type_split').val(response.item_type);
                    $('#item_type_split1').val(response.item_type);
                    $('#qty_pcs_split').val(response.qty_pcs);
                    $('#composition_split').val(response.composition);
                    $('#kode_warna_split').val(response.kode_warna);
                    $('#color_split').val(response.color);
                    $('#kgs_split').val(response.kgs);

                    hitungKgsSplit(); // Hitung otomatis setelah nilai dimasukkan

                    // **Tambahkan kode ini untuk menampilkan modal**
                    $('#modalSplitMaterial').modal('show');
                },
                error: function(xhr) {
                    alert('Gagal mengambil data material.');
                    console.error(xhr.responseText);
                }
            });
        });

        // Hitung ulang saat qty area diubah
        $('#qty1, #qty2').on('input', hitungKgsSplit);

        // Validasi & Kirim Data Saat Split
        $('#splitForm').on('submit', function(e) {
            let qtyAwal = parseFloat($('#qty_pcs_split').val()) || 0;
            let qty1 = parseFloat($('#qty1').val()) || 0;
            let qty2 = parseFloat($('#qty2').val()) || 0;
            let totalQty = qty1 + qty2;

            if (totalQty !== qtyAwal) {
                alert('Jumlah Qty Area 1 dan Area 2 harus sama dengan Qty Awal!');
                e.preventDefault();
                return;
            }

            // Kirim data ke backend untuk buat ID baru
            $.ajax({
                url: '<?= base_url($role . '/splitMaterial') ?>',
                type: 'POST',
                data: {
                    id_material_old: $('#id_material_split').val(),
                    id_order: $('#id_order_split').val(),
                    style_size: $('#style_size_split').val(),
                    inisial: $('#inisial_split').val(),
                    gw: $('#gw_split').val(),
                    loss: $('#loss_split').val(),
                    item_type: $('#item_type_split').val(),
                    composition: $('#composition_split').val(),
                    kode_warna: $('#kode_warna_split').val(),
                    color: $('#color_split').val(),
                    qty_pcs_1: qty1,
                    qty_pcs_2: qty2,
                    kgs_1: $('#kgs1').val(),
                    kgs_2: $('#kgs2').val(),
                    split_area_1: $('#split_area_1').val(),
                    split_area_2: $('#split_area_2').val()
                },
                success: function(response) {
                    alert('Material berhasil di-split!');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat split data.');
                    console.error(xhr.responseText);
                }
            });

            e.preventDefault(); // Stop form dari submit default
        });
    });
</script> -->
<?php $this->endSection(); ?>