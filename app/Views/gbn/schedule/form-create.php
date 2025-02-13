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

    /* Table Styles */
    .table-responsive {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background-color: rgb(0, 77, 94);
        color: #ffffff;
        font-weight: 600;
        text-transform: uppercase;
        padding: 15px;
    }

    .table tbody td {
        padding: 15px;
        vertical-align: middle;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 15px;
    }

    .form-control,
    .form-select {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 8px 12px;
        width: 100%;
        transition: border-color 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: rgb(0, 147, 152);
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .form-check-label {
        /* bold */
        font-weight: 600;

    }

    .form-check-input {
        height: 30px;
    }

    /* Button Styles */
    .btn {
        padding: 10px 20px;
        border-radius: 4px;
        font-weight: 600;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }


    .btn-danger {
        background-color: #e74c3c;
        border-color: #e74c3c;
    }

    .btn-danger:hover {
        background-color: #c0392b;
        border-color: #c0392b;
    }

    /* Badge Styles */





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
                                        <input type="number" min=0 class="form-control" id="sisa_kapasitas" name="sisa_kapasitas" value="<?= $max_caps ?>" data-max-caps="<?= $max_caps ?>" readonly>
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
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="poTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th class="text-center">Order</th>
                                                    <th class="text-center">
                                                        <button type="button" class="btn btn-info" id="addRow">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">1</td>
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="itemtype"> Item Type</label>
                                                                    <select class="form-select item-type" name="item_type[]" required>
                                                                        <option value="">Pilih Item Type</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="po">PO</label>
                                                                    <select class="form-select po-select" name="po[]" required>
                                                                        <option value="">Pilih PO</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <div class="form-group">
                                                                    <label for="tgl_start_mc">Tgl Start MC</label>
                                                                    <input type="date" class="form-control" name="tgl_start_mc[]" readonly>
                                                                </div>
                                                            </div>

                                                            <div class="col-4">
                                                                <div class="form-group">
                                                                    <label for="delivery_awal">Delivery Awal</label>
                                                                    <input type="date" class="form-control" name="delivery_awal[]" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="form-group ">
                                                                    <label for="delivery_akhir">Delivery Akhir</label>
                                                                    <input type="date" class="form-control" name="delivery_akhir[]" readonly>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <div class="form-group">
                                                                    <label for="qty_po">Qty PO</label>
                                                                    <input type="number" class="form-control" name="qty_po[]" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="form-group">
                                                                    <label for="qty_po_plus">Qty PO (+)</label>
                                                                    <input type="number" class="form-control" name="qty_po_plus[]" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="form-group">
                                                                    <label for="qty_celup">Qty Celup</label>
                                                                    <input type="number" step="0.01" min="0.01" class="form-control" name="qty_celup[]" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <div class="form-group">
                                                                    <label for="qty_celup">KG Kebutuhan :</label>
                                                                    <br />
                                                                    <span class="badge bg-info">
                                                                        <span class="kg_kebutuhan">0.00</span> KG <!-- Ganti id dengan class -->
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="form-group">
                                                                    <label for="qty_celup">Tagihan Sch :</label>
                                                                    <br />
                                                                    <span class="badge bg-info">
                                                                        <span class="sisa_jatah">0.00</span> KG <!-- Ganti id dengan class -->
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 d-flex align-items-center">
                                                                <div class="form-group">
                                                                    <label for="po_plus">PO +</label>
                                                                    <input type="checkbox" id="po_plus" class="form-control form-check-input" name="po_plus[]" value="1">
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </td>
                                                    <td class="text-center">

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

                                <!-- Tombol Submit -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-info w-100">Simpan Jadwal</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<!-- Add JavaScript to initialize Select2 -->

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const jenisBahanBaku = document.getElementById('jenis_bahan_baku');
        const suggestionsBox = document.querySelector('.suggestions-box');
        const kodeWarna = document.getElementById('kode_warna');
        const suggestionsBoxKWarna = document.getElementById('suggestionsKWarna');
        const warnaInput = document.getElementById('warna'); // Input untuk menampilkan warna
        const poTable = document.getElementById("poTable");
        const itemType = document.querySelector("select[name='item_type']"); // Pastikan ini adalah elemen <select>
        const poSelect = document.querySelector("select[name='po[]']"); // Pastikan ini adalah elemen <select>



        // ✅ Fungsi Fetch Data Kode Warna
        function fetchKodeWarnaSuggestions(query) {
            fetch('<?= base_url(session('role') . "/schedule/getKodeWarna") ?>?query=' + query)
                .then(response => response.json())
                .then(data => {
                    // console.log("Kode Warna Data:", data);
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
                    // console.log("Warna Data:", data);
                    // Pastikan ada data sebelum mengakses indeks pertama
                    warnaInput.value = data[0].color;
                    fetchItemType(kodeWarna, data[0].color);
                })
                .catch(error => {
                    console.error('Error fetching warna by kode warna:', error);
                    warnaInput.value = 'Error mengambil warna';
                });
        }
        kodeWarna.addEventListener('change', function() {
            kodeWarna.value = suggestionsBoxKWarna.textContent;
            // console.log(kodeWarna.value);
            const query = kodeWarna.value;
            fetchWarnaByKodeWarna(query);
        });

        // ✅ Event listener untuk kode_warna input serta tampilkan warna
        kodeWarna.addEventListener('input', function() {
            const query = kodeWarna.value;
            if (query.length >= 1) {
                fetchKodeWarnaSuggestions(query);
            } else {
                suggestionsBoxKWarna.style.display = 'none';
            }
        });



        function fetchItemType(kodeWarna, warna) {
            fetch(`<?= base_url(session('role') . "/schedule/getItemType") ?>?kode_warna=${kodeWarna}&warna=${warna}`)
                .then(response => response.json())
                .then(data => {
                    // console.log("Item Type Data:", data);
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

                        // Tambahkan event listener untuk menangani perubahan pilihan item type
                        $(itemType).on('change', function() {
                            const itemTypeValue = $(itemType).val(); // Gunakan .val() untuk mengambil nilai yang dipilih
                            // console.log("Item Type Value:", itemTypeValue);

                            // Panggil fetchPOByKodeWarna jika nilai item type terpilih
                            if (itemTypeValue) {
                                const poSelect = document.querySelector(".po-select");
                                fetchPOByKodeWarna(kodeWarna, warna, itemTypeValue, poSelect);
                            }
                        });


                    } else {
                        itemType.innerHTML = '<option value="">Tidak ada Item Type</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching item type data:', error);
                });
        }

        // ✅ function untuk fetch data PO by kode warna, warna, item type
        function fetchPOByKodeWarna(kodeWarna, warna, itemType, poSelect) {
            // Encode itemType jika perlu
            const itemTypeEncoded = encodeURIComponent(itemType);

            // Menyusun URL untuk pengambilan data PO
            const url = `<?= base_url(session('role') . "/schedule/getPO") ?>?kode_warna=${kodeWarna}&warna=${warna}&item_type=${itemTypeEncoded}`;

            // console.log("Request URL:", url); // Debugging URL
            // console.log("Item Type:", itemType);
            // console.log("Kode Warna:", kodeWarna);
            // console.log("Warna:", warna);

            // Gunakan fetch API untuk melakukan request
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    // console.log("PO Data:", data); // Debugging data

                    if (Array.isArray(data) && data.length > 0) {
                        poSelect.innerHTML = '<option value="">Pilih PO</option>'; // Reset PO select
                        // Menambahkan pilihan PO ke select dropdown
                        data.forEach(po => {
                            const option = document.createElement('option');
                            option.value = po.id_order;
                            option.textContent = po.no_model;
                            poSelect.appendChild(option);
                        });
                    } else {
                        poSelect.innerHTML = '<option value="">Tidak ada PO</option>';
                    }

                })
                .catch(error => {
                    console.error('Error fetching PO data:', error); // Debugging error
                    poSelect.innerHTML = '<option value="">Gagal mengambil PO</option>';
                });
        }

        // ✅ Fungsi untuk menghitung total_qty_celup dan memeriksa max_caps serta tagihan SCH
        function calculateTotalAndRemainingCapacity() {
            const qtyCelupInputs = document.querySelectorAll("input[name='qty_celup[]']");
            let totalQtyCelup = 0;

            // Hitung total qty celup
            qtyCelupInputs.forEach(input => {
                totalQtyCelup += parseFloat(input.value) || 0; // Pastikan nilai adalah angka
            });

            // Update total_qty_celup
            const totalQtyCelupElement = document.getElementById("total_qty_celup");
            if (totalQtyCelupElement) {
                totalQtyCelupElement.value = totalQtyCelup.toFixed(2); // Format 2 angka di belakang koma
            }

            // Ambil nilai max_caps
            const maxCaps = parseFloat(document.getElementById("max_caps").value) || 0;

            // Periksa apakah total_qty_celup melebihi max_caps
            if (totalQtyCelup > maxCaps) {
                alert("⚠️ Total Qty Celup melebihi Max Caps!");
                totalQtyCelupElement.classList.add("is-invalid");
                totalQtyCelupElement.focus();
            }

            // Periksa apakah qty_celup di setiap baris melebihi tagihan SCH di baris tersebut
            const rows = poTable.querySelectorAll("tbody tr");
            rows.forEach(row => {
                const qtyCelup = parseFloat(row.querySelector("input[name='qty_celup[]']").value) || 0;
                const tagihanSCH = parseFloat(row.querySelector(".sisa_jatah").textContent) || 0;

                if (qtyCelup > tagihanSCH) {
                    alert(`⚠️ Qty Celup di baris ini melebihi Tagihan SCH! (Tagihan SCH: ${tagihanSCH.toFixed(2)})`);
                    row.querySelector("input[name='qty_celup[]']").classList.add("is-invalid");
                    row.querySelector("input[name='qty_celup[]']").focus();
                    // reset qty celup
                    row.querySelector("input[name='qty_celup[]']").value = '';
                }
            });

            // Update sisa kapasitas
            const sisaKapasitasElement = document.getElementById("sisa_kapasitas");
            if (sisaKapasitasElement) {
                const sisaKapasitas = maxCaps - totalQtyCelup;
                sisaKapasitasElement.value = sisaKapasitas.toFixed(2); // Format 2 angka di belakang koma
                if (sisaKapasitas < 0) {
                    alert("⚠️ Sisa Kapasitas negatif!");
                    sisaKapasitasElement.classList.add("is-invalid");
                    sisaKapasitasElement.focus();
                } else {
                    sisaKapasitasElement.classList.remove("is-invalid");
                }
            }
        }

        // ✅ Event listener untuk menghitung total qty celup dan sisa kapasitas
        poTable.addEventListener("input", function(e) {
            if (e.target.name === "qty_celup[]") {
                calculateTotalAndRemainingCapacity();
            }
        });

        // ✅ Event listener untuk mengambil data PO
        poTable.addEventListener("change", function(e) {
            if (e.target.classList.contains("po-select")) {
                const poSelect = e.target;
                const selectedOption = poSelect.options[poSelect.selectedIndex];
                const tr = poSelect.closest("tr");

                // console.log("PO Select changed. Selected value:", selectedOption.value); // Log nilai yang dipilih

                // Ambil nilai dari elemen <select> dan <input>
                const itemTypeValue = tr.querySelector("select[name^='item_type']").value;
                const kodeWarnaValue = document.querySelector("input[name='kode_warna']").value; // Ambil nilai dari <input>

                // console.log("Item Type:", itemTypeValue); // Log nilai itemType
                // console.log("Kode Warna:", kodeWarnaValue); // Log nilai kodeWarna

                if (!itemTypeValue || !kodeWarnaValue) {
                    console.error("Item Type atau Kode Warna tidak boleh kosong.");
                    return;
                }

                if (selectedOption.value) {
                    // console.log("Fetching PO details for PO No:", selectedOption.value); // Log sebelum fetch
                    fetchPODetails(selectedOption.value, tr, itemTypeValue, kodeWarnaValue);
                } else {
                    // console.log("No PO selected. Resetting fields."); // Log jika tidak ada PO yang dipilih
                    // Reset fields if no PO is selected
                    const tglStartMC = tr.querySelector("input[name='tgl_start_mc[]']");
                    const deliveryAwal = tr.querySelector("input[name='delivery_awal[]']");
                    const deliveryAkhir = tr.querySelector("input[name='delivery_akhir[]']");
                    const qtyPO = tr.querySelector("input[name='qty_po[]']");
                    const qtyPOPlus = tr.querySelector("input[name='qty_po_plus[]']");
                    const kgKebutuhan = tr.querySelector(".kg_kebutuhan");
                    const sisaJatah = tr.querySelector(".sisa_jatah");

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
        function fetchPODetails(poNo, tr, itemType, kodeWarna) {
            const url = `<?= base_url(session('role') . "/schedule/getPODetails") ?>?id_order=${poNo}&item_type=${itemType}&kode_warna=${kodeWarna}`;
            // console.log("Request URL:", url); // Log URL yang digunakan untuk fetch

            fetch(url)
                .then(response => {
                    // console.log("Response received from server."); // Log saat response diterima
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    // console.log("Data received from server:", data); // Log data yang diterima dari server

                    if (data && !data.error) { // Pastikan tidak ada error
                        const tglStartMC = tr.querySelector("input[name='tgl_start_mc[]']");
                        const deliveryAwal = tr.querySelector("input[name='delivery_awal[]']");
                        const deliveryAkhir = tr.querySelector("input[name='delivery_akhir[]']");
                        const qtyPO = tr.querySelector("input[name='qty_po[]']");
                        const qtyPOPlus = tr.querySelector("input[name='qty_po_plus[]']");
                        const kgKebutuhan = tr.querySelector(".kg_kebutuhan");
                        const sisaJatah = tr.querySelector(".sisa_jatah");

                        // Isi data ke elemen HTML
                        tglStartMC.value = data.start_mesin || '';
                        deliveryAwal.value = data.delivery_awal || '';
                        deliveryAkhir.value = data.delivery_akhir || '';
                        // hanya 2 angka dibelakang koma
                        if (qtyPO.value == '') {
                            qtyPO.value = parseFloat(data.kg_kebutuhan).toFixed(2);
                        } else {
                            qtyPO.value = parseFloat(qtyPO.value).toFixed(2) || '';
                        }
                        qtyPOPlus.value = parseFloat(data.qty_po_plus).toFixed(2) || '';
                        kgKebutuhan.textContent = parseFloat(data.kg_kebutuhan).toFixed(2) || '0.00';
                        sisaJatah.textContent = parseFloat(data.sisa_jatah).toFixed(2) || '0.00';

                    } else {
                        console.error('Error fetching PO details:', data.error || 'No data found'); // Log error
                    }

                })
                .catch(error => {
                    console.error('Error fetching PO details:', error); // Log error jika fetch gagal
                });
        }


        // ✅ Event Listener untuk Menambah Baris Baru
        // Function to add a new row
        document.getElementById("addRow").addEventListener("click", function() {
            const tbody = poTable.querySelector("tbody");
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td class="text-center">${tbody.rows.length + 1}</td>
                <td>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="itemtype"> Item Type</label>
                                <select class="form-select item-type" name="item_type[]" required>
                                    <option value="">Pilih Item Type</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="po">PO</label>
                                <select class="form-select po-select" name="po[]" required>
                                    <option value="">Pilih PO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="tgl_start_mc">Tgl Start MC</label>
                                <input type="date" class="form-control" name="tgl_start_mc[]" readonly>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="form-group">
                                <label for="delivery_awal">Delivery Awal</label>
                                <input type="date" class="form-control" name="delivery_awal[]" readonly>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group ">
                                <label for="delivery_akhir">Delivery Akhir</label>
                                <input type="date" class="form-control" name="delivery_akhir[]" readonly>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="qty_po">Qty PO</label>
                                <input type="number" class="form-control" name="qty_po[]" readonly>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="qty_po_plus">Qty PO (+)</label>
                                <input type="number" class="form-control" name="qty_po_plus[]" readonly>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="qty_celup">Qty Celup</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" name="qty_celup[]" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="qty_celup">KG Kebutuhan :</label>
                                <span class="badge bg-info">
                                    <span class="kg_kebutuhan">0.00</span> KG <!-- Ganti id dengan class -->
                                </span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="qty_celup">Tagihan Sch :</label>

                                <span class="badge bg-info">
                                    <span class="sisa_jatah">0.00</span> KG <!-- Ganti id dengan class -->
                                </span>
                            </div>
                        </div>
                        <div class="col-4 d-flex align-items-center">
                            <div class="form-group">
                                <label for="po_plus">PO +</label>
                                <input type="checkbox" id="po_plus" class="form-control form-check-input" name="po_plus[]" value="1">
                            </div>
                        </div>
                    </div>

                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger removeRow">
                        <i class="fas fa-trash"></i>
                    </button>
                        
                </td>
            `;
            tbody.appendChild(newRow);

            // Mengisi opsi item_type di baris baru
            const itemTypeSelect = newRow.querySelector(".item-type");
            fetchItemTypeRow(kodeWarna.value, warnaInput.value, itemTypeSelect);

            // Menambahkan event listener untuk perubahan item_type di baris baru
            $(itemTypeSelect).on('change', function() {
                const itemTypeValue = $(this).val();
                const poSelect = newRow.querySelector(".po-select");
                fetchPOByKodeWarna(kodeWarna.value, warnaInput.value, itemTypeValue, poSelect);
            });

            // Menambahkan event listener untuk input qty_celup di baris baru
            newRow.querySelector("input[name='qty_celup[]']").addEventListener("input", function() {
                calculateTotalAndRemainingCapacity();
            });
        });

        // Fungsi fetchItemType yang dimodifikasi untuk menerima parameter itemTypeSelect
        function fetchItemTypeRow(kodeWarna, warna, itemTypeSelect) {
            fetch(`<?= base_url(session('role') . "/schedule/getItemType") ?>?kode_warna=${kodeWarna}&warna=${warna}`)
                .then(response => response.json())
                .then(data => {
                    // console.log("Item Type Data:", data);
                    if (data.length > 0) {
                        itemTypeSelect.innerHTML = '<option value="">Pilih Item Type</option>';
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.item_type;
                            option.textContent = item.item_type;
                            itemTypeSelect.appendChild(option);
                        });
                    } else {
                        itemTypeSelect.innerHTML = '<option value="">Tidak ada Item Type</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching item type data:', error);
                });
        }

        // Function to remove a row
        poTable.addEventListener("click", function(e) {
            if (e.target.classList.contains("removeRow")) {
                e.target.closest("tr").remove();
                calculateTotalAndRemainingCapacity(); // Hitung ulang setelah menghapus baris
            }
        });


    });
</script>

<?php $this->endSection(); ?>