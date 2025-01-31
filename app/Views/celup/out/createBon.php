<?php $this->extend($role . '/out/header'); ?>
<?php $this->section('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Input Bon Pengiriman</h3>
                </div>
                <div class="card-body">
                    <form action="<?= base_url($role . '/outCelup/saveBon/') ?>" method="post">
                        <div id="kebutuhan-container">
                            <label>Form Input Bon</label>
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">1</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                    <!-- Form Items -->
                                    <div class="kebutuhan-item">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label>No Model</label>
                                                <select class="form-control" name="tujuan_po" id="selectTujuan" required>
                                                    <option value="">Pilih No Model</option>
                                                    <option value="Celup Cones">QWERTY</option>
                                                    <option value="Covering">ZXCVB</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Item Type</label>
                                                <select class="form-control" name="tujuan_po" id="selectTujuan" required>
                                                    <option value="">Pilih Item Type</option>
                                                    <option value="Celup Cones">QWERTY</option>
                                                    <option value="Covering">ZXCVB</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Kode Warna</label>
                                                <select class="form-control" name="tujuan_po" id="selectTujuan" required>
                                                    <option value="">Pilih Kode Warna</option>
                                                    <option value="Celup Cones">QWERTY</option>
                                                    <option value="Covering">ZXCVB</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Warna</label>
                                                <select class="form-control" name="tujuan_po" id="selectTujuan" required>
                                                    <option value="">Pilih Warna</option>
                                                    <option value="Celup Cones">QWERTY</option>
                                                    <option value="Covering">ZXCVB</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Surat Jalan Section -->
                                        <div class="row g-3 mt-3">
                                            <div class="col-md-2">
                                                <label>Detail Surat Jalan</label>
                                                <select class="form-control" name="detail_sj" id="detail_sj" required>
                                                    <option value="">Pilih Surat Jalan</option>
                                                    <option value="COVER MAJALAYA">COVER MAJALAYA</option>
                                                    <option value="IMPOR DARI KOREA">IMPOR DARI KOREA</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>No Surat Jalan</label>
                                                <input type="text" class="form-control" id="no_surat_jalan" name="no_surat_jalan" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Tanggal Datang</label>
                                                <input type="date" class="form-control" id="tgl_datang" name="tgl_datang" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label>LMD</label>
                                                <select class="form-control" name="l_m_d" required>
                                                    <option value="">Pilih LMD</option>
                                                    <option value="L">L</option>
                                                    <option value="M">M</option>
                                                    <option value="D">D</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Harga</label>
                                                <input type="number" class="form-control" id="harga" name="harga" required>
                                            </div>
                                            <div class="col-md-1">
                                                <label for="ganti-retur" class="text-center">Ganti Retur</label>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label>
                                                            <input type="radio" name="ganti_retur" value="1"> Ya
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label>
                                                            <input type="radio" name="ganti_retur" value="0"> Tidak
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Additional Fields -->
                                        <div class="row g-3 mt-3">
                                            <div class="row g-3 mt-3">
                                                <div class="col-md-3">
                                                    <label>GW</label>
                                                    <input type="number" class="form-control" id="gw" name="gw" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>NW</label>
                                                    <input type="number" class="form-control" id="nw" name="nw" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Cones</label>
                                                    <input type="number" class="form-control" id="cones" name="cones" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Karung</label>
                                                    <input type="number" class="form-control" id="karung" name="karung" required>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Out Celup Section -->
                                        <div class="row g-3 mt-3">
                                            <div class="table-responsive">
                                                <table id="poTable" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th width=30 class="text-center">No</th>
                                                            <th class="text-center">GW Kirim</th>
                                                            <th class="text-center">Kgs Kirim</th>
                                                            <th class="text-center">Cones Kirim</th>
                                                            <th class="text-center">Lot Kirim</th>
                                                            <th class="text-center">
                                                                <button type="button" class="btn btn-info" id="addRow">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control text-center" name="identitas_krg[]" value="1" readonly></td>
                                                            <td><input type="number" class="form-control" name="gw_kirim[]" readonly></td>
                                                            <td><input type="number" class="form-control" name="kgs_kirim[]" readonly></td>
                                                            <td><input type="number" class="form-control" name="cones_kirim[]" readonly></td>
                                                            <td><input type="number" class="form-control" name="lot_kirim[]" readonly></td>
                                                            <td class="text-center">
                                                                <!-- <button type="button" class="btn btn-danger removeRow">
                                                                    <i class="fas fa-trash"></i>
                                                                </button> -->
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- Buttons -->
                                        <div class="row mt-3">
                                            <div class="col-12 text-center mt-2">
                                                <button class="btn btn-icon btn-3 btn-outline-info add-more" type="button">
                                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                                </button>
                                                <button class="btn btn-icon btn-3 btn-outline-danger remove" type="button">
                                                    <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-info w-100">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const navTab = document.getElementById("nav-tab");
        const navTabContent = document.getElementById("nav-tabContent");
        let tabIndex = 2;

        function updateTabNumbers() {
            // Update nomor pada setiap tab
            const tabButtons = navTab.querySelectorAll(".nav-link");
            const tabPanes = navTabContent.querySelectorAll(".tab-pane");

            tabButtons.forEach((button, index) => {
                const newNumber = index + 1;
                button.textContent = newNumber; // Update nomor tab
                button.dataset.bsTarget = `#nav-content-${newNumber}`;
                button.id = `nav-tab-${newNumber}-button`;

                const relatedPane = tabPanes[index];
                relatedPane.id = `nav-content-${newNumber}`;
                relatedPane.ariaLabelledby = `nav-tab-${newNumber}-button`;

                // Update nama atribut input agar sinkron
                relatedPane.querySelectorAll("[name]").forEach((input) => {
                    const name = input.name.replace(/\d+/, newNumber - 1);
                    input.name = name;
                });
            });

            // Perbarui indeks tab berikutnya
            tabIndex = tabButtons.length + 1;
        }

        // Fungsi untuk membuat tab baru
        function addNewTab() {
            // ID untuk tab dan konten baru
            const newTabId = `nav-tab-${tabIndex}`;
            const newContentId = `nav-content-${tabIndex}`;

            // Tambahkan tab baru ke nav-tab
            const newTabButton = document.createElement("button");
            newTabButton.className = "nav-link";
            newTabButton.id = `${newTabId}-button`;
            newTabButton.dataset.bsToggle = "tab";
            newTabButton.dataset.bsTarget = `#${newContentId}`;
            newTabButton.type = "button";
            newTabButton.role = "tab";
            newTabButton.ariaControls = newContentId;
            newTabButton.ariaSelected = "false";
            newTabButton.textContent = tabIndex;

            // Tambahkan tab button ke nav-tab
            navTab.appendChild(newTabButton);

            // Tambahkan konten baru ke tab-content
            const newTabPane = document.createElement("div");
            newTabPane.className = "tab-pane fade";
            newTabPane.id = newContentId;
            newTabPane.role = "tabpanel";
            newTabPane.ariaLabelledby = `${newTabId}-button`;

            // Tambahkan elemen `input-group` ke tab baru
            newTabPane.innerHTML = `
            <div class="kebutuhan-item">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label>No Model</label>
                                                <select class="form-control" name="tujuan_po" id="selectTujuan" required>
                                                    <option value="">Pilih No Model</option>
                                                    <option value="Celup Cones">QWERTY</option>
                                                    <option value="Covering">ZXCVB</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Item Type</label>
                                                <select class="form-control" name="tujuan_po" id="selectTujuan" required>
                                                    <option value="">Pilih Item Type</option>
                                                    <option value="Celup Cones">QWERTY</option>
                                                    <option value="Covering">ZXCVB</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Kode Warna</label>
                                                <select class="form-control" name="tujuan_po" id="selectTujuan" required>
                                                    <option value="">Pilih Kode Warna</option>
                                                    <option value="Celup Cones">QWERTY</option>
                                                    <option value="Covering">ZXCVB</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Warna</label>
                                                <select class="form-control" name="tujuan_po" id="selectTujuan" required>
                                                    <option value="">Pilih Warna</option>
                                                    <option value="Celup Cones">QWERTY</option>
                                                    <option value="Covering">ZXCVB</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Surat Jalan Section -->
                                        <div class="row g-3 mt-3">
                                            <div class="col-md-3">
                                                <label>Detail Surat Jalan</label>
                                                <select class="form-control" name="detail_sj" id="detail_sj" required>
                                                    <option value="">Pilih Surat Jalan</option>
                                                    <option value="COVER MAJALAYA">COVER MAJALAYA</option>
                                                    <option value="IMPOR DARI KOREA">IMPOR DARI KOREA</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>No Surat Jalan</label>
                                                <input type="text" class="form-control" id="no_surat_jalan" name="no_surat_jalan" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Tanggal Datang</label>
                                                <input type="date" class="form-control" id="tgl_datang" name="tgl_datang" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label>LMD</label>
                                                <select class="form-control" name="l_m_d" required>
                                                    <option value="">Pilih LMD</option>
                                                    <option value="L">L</option>
                                                    <option value="M">M</option>
                                                    <option value="D">D</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Harga</label>
                                                <input type="number" class="form-control" id="harga" name="harga" required>
                                            </div>
                                        </div>

                                        <!-- Additional Fields -->
                                        <div class="row g-3 mt-3">
                                            <div class="col-md-2">
                                                <label>GW</label>
                                                <input type="number" class="form-control" id="gw" name="gw" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label>NW</label>
                                                <input type="number" class="form-control" id="nw" name="nw" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Cones</label>
                                                <input type="number" class="form-control" id="cones" name="cones" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Karung</label>
                                                <input type="number" class="form-control" id="karung" name="karung" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Ganti Retur</label>
                                                <select class="form-control" name="ganti_retur">
                                                    <option value="">Pilih</option>
                                                    <option value="1">Ya</option>
                                                    <option value="0">Tidak</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Out Celup Section -->
                                        <div class="row g-3 mt-3">
                                            <div class="col-md-3">
                                                <label>GW Kirim</label>
                                                <input type="number" class="form-control" id="gw_kirim" name="gw_kirim" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Kgs Kirim</label>
                                                <input type="number" class="form-control" id="kgs_kirim" name="kgs_kirim" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Cones Kirim</label>
                                                <input type="number" class="form-control" id="cones_kirim" name="cones_kirim" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Lot Kirim</label>
                                                <input type="text" class="form-control" id="lot_kirim" name="lot_kirim" required>
                                            </div>
                                        </div>
                                        <!-- Buttons -->
                                        <div class="row mt-3">
                                            <div class="col-12 text-center mt-2">
                                                <button class="btn btn-icon btn-3 btn-outline-info add-more" type="button">
                                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                                </button>
                                                <button class="btn btn-icon btn-3 btn-outline-danger remove" type="button">
                                                    <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
            `;

            navTabContent.appendChild(newTabPane);

            // Pindahkan ke tab baru
            const bootstrapTab = new bootstrap.Tab(newTabButton);
            bootstrapTab.show();

            // Event listener tombol
            newTabPane.querySelector(".add-more").addEventListener("click", addNewTab);
            newTabPane.querySelector(".remove-tab").addEventListener("click", function() {
                removeTab(newTabButton, newTabPane);
            });

            tabIndex++;
        }

        // Fungsi untuk menghapus tab dan kontennya
        function removeTab(tabButton, tabPane) {
            if (navTab.children.length > 1) {
                tabButton.remove();
                tabPane.remove();
                updateTabNumbers();
                // Pindahkan ke tab pertama jika tab aktif dihapus
                const firstTab = navTab.querySelector("button");
                if (firstTab) {
                    const bootstrapTab = new bootstrap.Tab(firstTab);
                    bootstrapTab.show();
                }
            } else {
                alert("Minimal harus ada satu tab.");
            }
        }

        // Event listener untuk tombol "Add More" di tab pertama
        const addMoreButton = document.querySelector(".add-more");
        addMoreButton.addEventListener("click", addNewTab);

        const removeButton = document.querySelector(".remove-tab");
        removeButton.addEventListener("click", function() {
            const firstTabButton = navTab.querySelector(".nav-link");
            const firstTabPane = navTabContent.querySelector(".tab-pane");
            removeTab(firstTabButton, firstTabPane);
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

    // Event listener untuk baris baru
    document.getElementById("addRow").addEventListener("click", function() {
        const tbody = poTable.querySelector("tbody");
        const newIndex = tbody.querySelectorAll("tr").length + 1;
        const newRow = `
    <tr>
        <td><input type="text" class="form-control text-center" name="identitas_krg[]" value="${newIndex}" readonly></td>
        <td><input type="number" class="form-control" name="gw_kirim[]" readonly></td>
        <td><input type="number" class="form-control" name="kgs_kirim[]" readonly></td>
        <td><input type="number" class="form-control" name="cones_kirim[]" readonly></td>
        <td><input type="number" class="form-control" name="lot_kirim[]" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-danger removeRow">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>`;
        tbody.insertAdjacentHTML("beforeend", newRow);
        updatePODropdown(); // Perbarui dropdown PO di baris baru
        // Re-query qty_celup inputs after adding a new row
        qtyCelupInputs = document.querySelectorAll('input[name="qty_celup[]"]');
        // Add event listener for new qty_celup input fields
        qtyCelupInputs.forEach(input => {
            input.addEventListener('input', calculateCapacity);
        });

        // Recalculate capacity
        calculateCapacity(); // Update capacity when a new row is added
    });

    // Event delegation untuk menghapus baris
    poTable.addEventListener("click", function(e) {
        if (e.target.classList.contains("removeRow") || e.target.closest(".removeRow")) {
            e.target.closest("tr").remove();
            calculateCapacity(); // Recalculate capacity after row removal
        }
    });
</script>


<?php $this->endSection(); ?>