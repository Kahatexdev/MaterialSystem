<?php $this->extend($role . '/out/header'); ?>
<?php $this->section('content'); ?>
<style>
    @media (min-width: 992px) {
        .modal-dialog-custom {
            max-width: 90%;
        }
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

    <div class="card card-frame">
        <div class="card-body d-flex justify-content-between align-items-center" style="min-height: 80px;">
            <div class="text-header">
                <h3 class="m-0">Out Celup</h3>
            </div>
            <a href="<?= base_url($role . '/createBon') ?>" class="btn btn-info">
                <i class="ni ni-single-copy-04 me-2"></i>Create BON
            </a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-compact table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center align-middle">No </th>
                            <th class="text-center align-middle">No Model</th>
                            <th class="text-center align-middle">Tanggal Kirim</th>
                            <th class="text-center align-middle">No Surat Jalan</th>
                            <th class="text-center align-middle">Detail Surat Jalan</th>
                            <th class="text-center align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($outCelup as $out) : ?>
                            <tr>
                                <td class="text-center align-middle"><?= $no++ ?></td>
                                <td class="text-center align-middle"><?= $out['no_model_list'] ?></td>
                                <td class="text-center align-middle"><?= $out['tgl_datang'] ?></td>
                                <td class="text-center align-middle"><?= $out['no_surat_jalan'] ?></td>
                                <td class="text-center align-middle"><?= $out['detail_sj'] ?></td>
                                <td class="text-center align-middle">
                                    <button class="btn bg-gradient-dark btn-detail" data-id="<?= $out['id_bon'] ?>" data-toggle="modal" data-target="#detailModal">Detail</button>
                                    <a href="<?= base_url($role . '/generate/' . $out['id_bon']) ?>" class="btn bg-gradient-info">Barcode</a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-custom" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Bon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ni ni-fat-remove"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">No Model</th>
                                <th class="text-center">Item Type</th>
                                <th class="text-center">Kode Warna</th>
                                <th class="text-center">Warna</th>
                                <th class="text-center">LMD</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Ganti Retur</th>
                                <th class="text-center">GW Kirim</th>
                                <th class="text-center">Kgs Kirim</th>
                                <th class="text-center">Cones Kirim</th>
                                <th class="text-center">Lot Kirim</th>
                            </tr>
                        </thead>
                        <tbody id="detailModalBody">
                            <!-- Data akan dimasukkan di sini -->
                        </tbody>
                    </table>
                    <tfoot>
                        <div class="d-flex justify-content-end" id="footerModal">

                        </div>
                    </tfoot>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Bon -->
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();

        // Event listener untuk tombol detail
        $(document).on("click", ".btn-detail", function() {
            let id_bon = $(this).data("id");

            // Fetch data dari server
            $.ajax({
                url: "<?= base_url($role . '/outCelup/getDetail/') ?>" + id_bon,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    let detailBody = $("#detailModalBody");
                    let footerModal = $("#footerModal");

                    detailBody.empty(); // Hapus isi sebelumnya
                    footerModal.empty(); // Hapus footer sebelumnya

                    if (data.error) {
                        detailBody.html(`<tr><td colspan="11" class="text-center">${data.error}</td></tr>`);
                    } else {
                        $.each(data, function(index, item) {
                            let gantiReturText = item.ganti_retur == 1 ? "Ya" : "Tidak";
                            detailBody.append(`
                            <tr>
                                <td class="text-center">${item.no_model}</td>
                                <td class="text-center">${item.item_type}</td>
                                <td class="text-center">${item.kode_warna}</td>
                                <td class="text-center">${item.warna}</td>
                                <td class="text-center">${item.l_m_d}</td>
                                <td class="text-center">${item.harga}</td>
                                <td class="text-center">${gantiReturText}</td>
                                <td class="text-center">${item.gw_kirim}</td>
                                <td class="text-center">${item.kgs_kirim}</td>
                                <td class="text-center">${item.cones_kirim}</td>
                                <td class="text-center">${item.lot_kirim}</td>
                            </tr>
                        `);
                        });
                    }

                    // Tambahkan tombol edit dan delete ke footer modal
                    footerModal.append(`
                    <a type="button" href="<?= base_url($role . '/outCelup/editBon/') ?>${id_bon}" 
                        class="btn btn-warning btn-edit me-2">Edit</a>
                    <button type="button" class="btn btn-danger btn-delete" data-id="${id_bon}">Delete</button>
                `);

                    // Menampilkan modal setelah data di-load
                    $("#detailModal").modal("show");
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching detail:", error);
                    alert("Terjadi kesalahan saat mengambil data detail.");
                }
            });
        });
    });

    // Event listener untuk tombol delete (AJAX DELETE)
    $(document).on("click", ".btn-delete", function() {
        let id_bon = $(this).data("id");

        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url($role . '/outCelup/deleteBon/') ?>" + id_bon,
                    type: "DELETE",
                    success: function(response) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "Data berhasil dihapus.",
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $("#detailModal").modal("hide"); // Tutup modal
                            location.reload(); // Refresh halaman
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: "Error!",
                            text: "Gagal menghapus data.",
                            icon: "error"
                        });
                    }
                });
            }
        });
    });
</script>

<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Tambahkan event listener ke semua tombol delete dalam modal
        document.querySelectorAll(".btn-delete").forEach(button => {
            button.addEventListener("click", function() {
                let id = this.getAttribute("data-id-delete");

                Swal.fire({
                    title: "Konfirmasi Hapus",
                    text: "Apakah Anda yakin ingin menghapus data ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, Hapus!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("<?= base_url($role . '/outCelup/deleteBon/') ?>" + id, {
                                method: "DELETE",
                                headers: {
                                    "Content-Type": "application/json"
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire("Terhapus!", "Data berhasil dihapus.", "success").then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus.", "error");
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
                            });
                    }
                });
            });
        });
    });
</script> -->

<?php $this->endSection(); ?>