<?php $this->extend($role . '/schedule/header'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Mengurangi ukuran font untuk catatan kecil */
    small.text-warning {
        font-size: 0.8em;
        color: #ffc107;
        /* Warna kuning */
    }

    .select2-container {
        width: 100% !important;
        /* Pastikan Select2 menyesuaikan dengan lebar container */
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        /* Sesuaikan dengan desain form lainnya */
        border: 1px solid #ced4da;
        /* Gaya default untuk input */
        border-radius: 0.25rem;
        /* Tambahkan border radius agar seragam */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        /* Tengah secara vertikal */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        /* Tinggi ikon panah */
    }

    .suggestions-box {
        position: absolute;
        border: 1px solid #ccc;
        background-color: #fff;
        max-height: 150px;
        overflow-y: auto;
        width: calc(100% - 30px);
        /* Mengurangi lebar agar tidak melebihi parent-nya */
        z-index: 1000;
        box-sizing: border-box;
        /* Memastikan padding dan border termasuk dalam lebar */
        margin-top: 5px;
        /* Jarak antara input dan kotak saran */
        border-radius: 4px;
        /* Memberikan sudut yang sedikit melengkung */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* Menambahkan shadow untuk efek elevasi */
        left: 15px;
        /* Menyesuaikan posisi agar sejajar dengan input */
        right: 15px;
        /* Menyesuaikan posisi agar sejajar dengan input */
    }

    .suggestions-box div {
        padding: 8px;
        cursor: pointer;
        font-size: 14px;
        /* Ukuran font yang sesuai */
        color: #333;
        /* Warna teks yang mudah dibaca */
    }

    .suggestions-box div:hover {
        background-color: #f0f0f0;
        /* Warna latar saat hover */
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .suggestions-box {
            max-height: 120px;
            /* Mengurangi tinggi maksimal untuk layar kecil */
            font-size: 12px;
            /* Mengurangi ukuran font untuk layar kecil */
            width: calc(100% - 20px);
            /* Lebar lebih kecil untuk layar kecil */
            left: 10px;
            /* Menyesuaikan posisi untuk layar kecil */
            right: 10px;
            /* Menyesuaikan posisi untuk layar kecil */
        }

        .suggestions-box div {
            padding: 6px;
            /* Mengurangi padding untuk layar kecil */
        }
    }

    @media (max-width: 480px) {
        .suggestions-box {
            max-height: 100px;
            /* Lebih kecil lagi untuk layar sangat kecil */
            font-size: 11px;
            /* Ukuran font lebih kecil */
            width: calc(100% - 10px);
            /* Lebar lebih kecil untuk layar sangat kecil */
            left: 5px;
            /* Menyesuaikan posisi untuk layar sangat kecil */
            right: 5px;
            /* Menyesuaikan posisi untuk layar sangat kecil */
        }

        .suggestions-box div {
            padding: 4px;
            /* Padding lebih kecil */
        }
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Input Schedule Celup</h3>
                    <!-- text keterangan -->
                    <div class="card-tools">
                        <h6 class="badge bg-info text-white">Tanggal Schedule : <?= $tanggal_schedule ?> | Lot Urut : <?= $lot_urut ?></h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="<?= base_url(session('role') . '/schedule/saveSchedule') ?>" method="post">
                            <div class="row">
                                <div class="col-md-3">
                                    <!-- No Mesin -->
                                    <div class="mb-3">
                                        <label for="no_mesin" class="form-label">No Mesin</label>
                                        <input type="text" class="form-control" id="no_mesin" name="no_mesin" value="<?= $no_mesin ?>" readonly>
                                        <input type="hidden" name="tanggal_schedule" value="<?= $tanggal_schedule ?>">
                                        <input type="hidden" name="lot_urut" value="<?= $lot_urut ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- min caps -->
                                    <div class="mb-3">
                                        <label for="min_caps" class="form-label">Min Caps</label>
                                        <input type="number" class="form-control" id="min_caps" name="min_caps" value="<?= $min_caps ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Max caps -->
                                    <div class="mb-3">
                                        <label for="max_caps" class="form-label">Max Caps</label>
                                        <input type="number" class="form-control" id="max_caps" name="max_caps" value="<?= $max_caps ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Sisa Kapasitas hitung pakai JS -->
                                    <div class="mb-3">
                                        <label for="sisa_kapasitas" class="form-label">Sisa Kapasitas</label>
                                        <input type="number" class="form-control" id="sisa_kapasitas" name="sisa_kapasitas" value="<?= $max_caps ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Jenis Bahan baku -->
                                    <div class="mb-3">
                                        <label for="jenis_bahan_baku" class="form-label">Jenis Bahan Baku</label>
                                        <!-- select with search -->
                                        <select class="form-select" id="jenis_bahan_baku" name="jenis_bahan_baku" required>
                                            <option value="">Pilih Jenis Bahan Baku</option>
                                            <?php foreach ($jenis_bahan_baku as $bahan): ?>
                                                <option value="<?= $bahan['jenis'] ?>"><?= $bahan['jenis'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <!-- Kode Warna -->
                                    <div class="mb-3">
                                        <label for="kode_warna" class="form-label">Kode Warna</label>
                                        <input type="text" class="form-control" id="kode_warna" name="kode_warna" required>
                                        <div id="suggestionsKWarna" class="suggestions-box" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Warna -->
                                    <div class="mb-3">
                                        <label for="warna" class="form-label">Warna</label>
                                        <input type="text" class="form-control" id="warna" name="warna" maxlength="32" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- form input addmore-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="poTable" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Item Type</th>
                                                        <th class="text-center">PO</th>
                                                        <th class="text-center">Tgl Start MC</th>
                                                        <th class="text-center">Delivery Awal</th>
                                                        <th class="text-center">Delivery Akhir</th>
                                                        <th class="text-center">Qty PO</th>
                                                        <th class="text-center">Qty PO(+)</th>
                                                        <th class="text-center">Kg Kebutuhan</th>
                                                        <th class="text-center">Tagihan SCH</th>
                                                        <th class="text-center">Qty Celup
                                                        </th>
                                                        <th class="text-center">PO(+)</th>
                                                        <th class="text-center">
                                                            <button type="button" class="btn btn-info" id="addRow">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <select class="form-select item-type" name="item_type[]" required>
                                                                <option value="">Pilih Item Type</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-select po-select" name="po[]" required>
                                                                <option value="">Pilih PO</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="date" class="form-control" name="tgl_start_mc[]" readonly></td>
                                                        <td><input type="date" class="form-control" name="delivery_awal[]" readonly></td>
                                                        <td><input type="date" class="form-control" name="delivery_akhir[]" readonly></td>
                                                        <td><input type="number" class="form-control" name="qty_po[]" readonly></td>
                                                        <td><input type="number" class="form-control" name="qty_po_plus[]" readonly></td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info">
                                                                <span class="kg_kebutuhan">0.00</span> KG <!-- Ganti id dengan class -->
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info">
                                                                <span class="sisa_jatah">0.00</span> KG <!-- Ganti id dengan class -->
                                                            </span>
                                                        </td>
                                                        <td><input type="number" step="0.01" min="0.01" class="form-control" name="qty_celup[]" required></td>
                                                        <td>
                                                            <select class="form-select" name="po_plus[]" required>
                                                                <option value="">Pilih PO(+)</option>
                                                                <option value="1">Ya</option>
                                                                <option value="0">Tidak</option>
                                                            </select>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger removeRow">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="text-center">
                                                            <strong>Total Qty Celup</strong>
                                                        </td>
                                                        <td colspan="8" class="text-center">
                                                            <input type="number" class="form-control" id="total_qty_celup" name="total_qty_celup" value="0" readonly>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Submit -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-info w-100">Simpan Jadwal</button>
                                </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Add JavaScript to initialize Select2 -->

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const jenisBahanBaku = document.getElementById('jenis_bahan_baku');
        const suggestionsBox = document.querySelector('.suggestions-box');
        const kodeWarna = document.getElementById('kode_warna');
        const suggestionsBoxKWarna = document.getElementById('suggestionsKWarna');
        const warnaInput = document.getElementById('warna'); // Input untuk menampilkan warna
        const poTable = document.getElementById("poTable");
        const itemType = document.querySelector(".item-type");
        const poSelect = document.querySelector("select[name='po[]']");



        // ✅ Event listener untuk kode_warna input serta tampilkan warna
        // Pastikan `fetchPOByKodeWarna` juga dipanggil saat kode warna atau warna diinput
        kodeWarna.addEventListener('input', function() {
            const query = kodeWarna.value;
            if (query.length >= 3) {
                fetchKodeWarnaSuggestions(query);
                fetchWarnaByKodeWarna(query);
            } else {
                suggestionsBoxKWarna.style.display = 'none';
            }
        });

        warnaInput.addEventListener('input', function() {
            const kodeWarna = kodeWarna.value;
            const warna = warnaInput.value;
            const itemType = itemType.value;

            if (kodeWarna && warna && itemType) {
                fetchPOByKodeWarna(kodeWarna, warna, itemType, poSelect);
            }
        });

        // ✅ Fungsi Fetch Data Kode Warna
        function fetchKodeWarnaSuggestions(query) {
            fetch('<?= base_url(session('role') . "/schedule/getKodeWarna") ?>?query=' + query)
                .then(response => response.json())
                .then(data => {
                    console.log("Kode Warna Data:", data);
                    const kodeWarnaSuggestions = data.map(item => item.kode_warna);
                    displayKodeWarnaSuggestions(kodeWarnaSuggestions);
                })
                .catch(error => {
                    console.error('Error fetching kode warna suggestions:', error);
                });
        }

        // ✅ Fungsi Menampilkan Kode Warna Suggestion
        function displayKodeWarnaSuggestions(suggestions) {
            suggestionsBoxKWarna.innerHTML = ''; // Clear previous suggestions
            if (suggestions.length > 0) {
                suggestionsBoxKWarna.style.display = 'block'; // Show suggestions box
                suggestions.forEach(suggestion => {
                    const suggestionDiv = document.createElement('div');
                    suggestionDiv.textContent = suggestion;
                    suggestionDiv.addEventListener('click', function() {
                        kodeWarna.value = suggestion;
                        suggestionsBoxKWarna.style.display = 'none';
                    });
                    suggestionsBoxKWarna.appendChild(suggestionDiv);
                });
            } else {
                suggestionsBoxKWarna.style.display = 'none';
            }
        }

        // ✅ Fungsi Fetch Data Warna berdasarkan Kode Warna
        function fetchWarnaByKodeWarna(kodeWarna) {
            fetch('<?= base_url(session('role') . "/schedule/getWarna") ?>?kode_warna=' + kodeWarna)
                .then(response => response.json())
                .then(data => {
                    console.log("Warna Data:", data);
                    // Pastikan ada data sebelum mengakses indeks pertama
                    warnaInput.value = data[0].color;
                    fetchItemType(kodeWarna, data[0].color);
                })
                .catch(error => {
                    console.error('Error fetching warna by kode warna:', error);
                    warnaInput.value = 'Error mengambil warna';
                });
        }

        function fetchItemType(kodeWarna, Warna) {
            fetch('<?= base_url(session('role') . "/schedule/getItemType") ?>?kode_warna=' + kodeWarna + '&warna=' + Warna)
                .then(response => response.json())
                .then(data => {
                    console.log("Item Type Data:", data);
                    const itemType = document.querySelector(".item-type");

                    if (data.length > 0) {
                        itemType.innerHTML = '<option value="">Pilih Item Type</option>';

                        // Menambahkan option berdasarkan data yang diterima
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.item_type;
                            option.textContent = item.item_type;
                            itemType.appendChild(option);
                        });

                        // Inisialisasi Select2
                        $(itemType).select2({
                            placeholder: "Pilih item type",
                            allowClear: true
                        });

                        // Setel nilai yang dipilih setelah Select2 diinisialisasi
                        // Jika ada item type yang dipilih sebelumnya, setel ke nilai yang sesuai
                        $(itemType).val(data[0].item_type).trigger('change');

                        // Ambil nilai itemType setelah pemilihan
                        const itemTypeValue = itemType.value;
                        console.log("Item Type Value:", itemTypeValue);

                        // Panggil fetchPOByKodeWarna
                        fetchPOByKodeWarna(kodeWarna, Warna, itemTypeValue, poSelect);

                    } else {
                        itemType.innerHTML = '<option value="">Tidak ada Item Type</option>';
                        $(itemType).select2('destroy');
                    }
                })
                .catch(error => {
                    console.error('Error fetching item type data:', error);
                });
        }




        // Inisialisasi Select2 pada elemen yang sesuai
        $(document).ready(function() {
            $(".item-type").select2({
                placeholder: "Pilih item type",
                allowClear: true
            });
        });

        // ✅ function untuk fetch data PO by kode warna, warna, item type
        function fetchPOByKodeWarna(kodeWarna, warna, itemType, poSelect) {
            // const itemType = encodeURIComponent(itemType);
            const itemTypeEncoded = encodeURIComponent(itemType);
            // Menyusun URL untuk pengambilan data PO
            const url = `<?= base_url(session('role') . "/schedule/getPO") ?>?kode_warna=${kodeWarna}&warna=${warna}&item_type=${itemTypeEncoded}`;
            console.log("Request URL:", url); // Debugging URL
            console.log("Item Type:", itemType);
            console.log("Kode Warna:", kodeWarna);
            console.log("Warna:", warna);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    console.log("PO Data:", data); // Debugging

                    // Memastikan data PO diterima dengan benar
                    if (Array.isArray(data) && data.length > 0) {
                        poSelect.innerHTML = '<option value="">Pilih PO</option>'; // Reset PO select

                        // Menambahkan pilihan PO ke select dropdown
                        data.forEach(po => {
                            const option = document.createElement('option');
                            option.value = po.no_model;
                            option.textContent = po.no_model;
                            poSelect.appendChild(option);
                        });
                    } else {
                        poSelect.innerHTML = '<option value="">Tidak ada PO</option>';
                    }

                    // Memastikan Select2 tetap berfungsi setelah pembaruan data
                    $(poSelect).select2({
                        placeholder: "Pilih PO",
                        allowClear: true
                    });

                })
                .catch(error => {
                    console.error('Error fetching PO data:', error);
                    poSelect.innerHTML = '<option value="">Gagal mengambil PO</option>';
                });
        }


        // ✅ Event listener untuk mengambil data PO
        poTable.addEventListener("change", function(e) {
            if (e.target.classList.contains("po-select")) {
                const poSelect = e.target;
                const selectedOption = poSelect.options[poSelect.selectedIndex];
                const tr = poSelect.closest("tr");

                const tglStartMC = tr.querySelector("input[name='tgl_start_mc[]']");
                const deliveryAwal = tr.querySelector("input[name='delivery_awal[]']");
                const deliveryAkhir = tr.querySelector("input[name='delivery_akhir[]']");
                const qtyPO = tr.querySelector("input[name='qty_po[]']");
                const qtyPOPlus = tr.querySelector("input[name='qty_po_plus[]']");
                const kgKebutuhan = tr.querySelector(".kg_kebutuhan");
                const sisaJatah = tr.querySelector(".sisa_jatah");

                if (selectedOption.value) {
                    fetchPODetails(selectedOption.value, tr);
                } else {
                    // Reset fields if no PO is selected
                    tglStartMC.value = '';
                    deliveryAwal.value = '';
                    deliveryAkhir.value = '';
                    qtyPO.value = '';
                    qtyPOPlus.value = '';
                    kgKebutuhan.textContent = '0.00';
                    sisaJatah.textContent = '0.00';
                }
            }
        });


        // ✅ Event listener untuk mengambil detail PO
        poTable.addEventListener("change", function(e) {
            if (e.target.classList.contains("po-select")) {
                const poSelect = e.target;
                const selectedOption = poSelect.options[poSelect.selectedIndex];
                const tr = poSelect.closest("tr");

                const tglStartMC = tr.querySelector("input[name='tgl_start_mc[]']");
                const deliveryAwal = tr.querySelector("input[name='delivery_awal[]']");
                const deliveryAkhir = tr.querySelector("input[name='delivery_akhir[]']");
                const qtyPO = tr.querySelector("input[name='qty_po[]']");
                const qtyPOPlus = tr.querySelector("input[name='qty_po_plus[]']");
                const kgKebutuhan = tr.querySelector(".kg_kebutuhan");
                const sisaJatah = tr.querySelector(".sisa_jatah");

                if (selectedOption.value) {
                    fetchPODetails(selectedOption.value, tr);
                } else {
                    // Reset fields if no PO is selected
                    tglStartMC.value = '';
                    deliveryAwal.value = '';
                    deliveryAkhir.value = '';
                    qtyPO.value = '';
                    qtyPOPlus.value = '';
                    kgKebutuhan.textContent = '0.00';
                    sisaJatah.textContent = '0.00';
                }
            }
        });


        // ✅ Fungsi Fetch Detail PO
        function fetchPODetails(poNo, tr) {
            const url = `<?= base_url(session('role') . "/schedule/getPO") ?>?kode_warna=${kodeWarna}&warna=${warna}&item_type=${itemType}`;
            console.log("Request URL:", url); // Debugging URL

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        const tglStartMC = tr.querySelector("input[name='tgl_start_mc[]']");
                        const deliveryAwal = tr.querySelector("input[name='delivery_awal[]']");
                        const deliveryAkhir = tr.querySelector("input[name='delivery_akhir[]']");
                        const qtyPO = tr.querySelector("input[name='qty_po[]']");
                        const qtyPOPlus = tr.querySelector("input[name='qty_po_plus[]']");
                        const kgKebutuhan = tr.querySelector(".kg_kebutuhan");
                        const sisaJatah = tr.querySelector(".sisa_jatah");

                        tglStartMC.value = data.tgl_start_mc;
                        deliveryAwal.value = data.delivery_awal;
                        deliveryAkhir.value = data.delivery_akhir;
                        qtyPO.value = data.qty_po;
                        qtyPOPlus.value = data.qty_po_plus;
                        kgKebutuhan.textContent = data.kg_kebutuhan;
                        sisaJatah.textContent = data.sisa_jatah;
                    } else {
                        console.error('No PO details found');
                    }
                })
                .catch(error => {
                    console.error('Error fetching PO details:', error);
                });
        }


        // ✅ Event listener untuk menghitung total qty celup
        poTable.addEventListener("input", function(e) {
            if (e.target.classList.contains("qty_celup")) {
                const qtyCelupInputs = poTable.querySelectorAll("input[name='qty_celup[]']");
                let totalQtyCelup = 0;
                qtyCelupInputs.forEach(input => {
                    totalQtyCelup += parseFloat(input.value) || 0;
                });
                document.getElementById("total_qty_celup").value = totalQtyCelup;
            }
        });







        // ✅ Event Listener untuk Menambah Baris Baru
        document.getElementById("addRow").addEventListener("click", function() {
            const tbody = poTable.querySelector("tbody");
            const newRow = `
        <tr>
            <td>
                <select class="form-select item-type" name="item_type[]" required>
                    <option value="">Pilih Item Type</option>
                </select>
            </td>
            <td>
                <select class="form-select po-select" name="po[]" required>
                    <option value="">Pilih PO</option>
                </select>
            </td>
            <td><input type="date" class="form-control" name="tgl_start_mc[]" readonly></td>
            <td><input type="date" class="form-control" name="delivery_awal[]" readonly></td>
            <td><input type="date" class="form-control" name="delivery_akhir[]" readonly></td>
            <td><input type="number" class="form-control" name="qty_po[]" readonly></td>
            <td><input type="number" class="form-control" name="qty_po_plus[]" readonly></td>
            <td class="text-center">
                <span class="badge bg-info">
                    <span class="kg_kebutuhan">0.00</span> KG
                </span>
            </td>
            <td class="text-center">
                <span class="badge bg-info">
                    <span class="sisa_jatah">0.00</span> KG
                </span>
            </td>
            <td><input type="number" step="0.01" min="0.01" class="form-control" name="qty_celup[]" required></td>
            <td>
                <select class="form-select" name="po_plus[]" required>
                    <option value="">Pilih PO(+)</option>
                    <option value="1">Ya</option>
                    <option value="0">Tidak</option>
                </select>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger removeRow">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
            tbody.insertAdjacentHTML("beforeend", newRow);

        });

        // ✅ Event Delegation untuk Menghapus Baris
        poTable.addEventListener("click", function(e) {
            if (e.target.classList.contains("removeRow") || e.target.closest(".removeRow")) {
                e.target.closest("tr").remove();
            }
        });
    });
</script>

<?php $this->endSection(); ?>