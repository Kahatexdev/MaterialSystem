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

    legend {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 8px;
    }

    fieldset {
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .col-4.d-flex {
        gap: 10px;
    }

    .form-group div {
        display: flex;
        align-items: center;
        gap: 15px;
    }


    input[type="radio"] {
        margin-right: 5px;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-start align-items-sm-center flex-column flex-sm-row gap-2">
                    <div>
                        <h3 class="card-title mb-1">Form Input Schedule Celup Sample</h3>
                        <div class="card-tools">
                            <h6 class="badge bg-info text-white mb-0">
                                Tanggal Schedule : <?= $tanggal_schedule ?> | Lot Urut : <?= $lot_urut ?>
                            </h6>
                        </div>
                    </div>
                    <div class="form-sample">

                        <a href="<?= base_url($role . '/schedule/form?no_mesin=' . $no_mesin . '&tanggal_schedule=' . $tanggal_schedule . '&lot_urut=' . $lot_urut) ?>" class="btn btn-info btn-sm px-1 py-1" style=" font-size: 1rem;">
                            Mesin Normal
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <form action="<?= base_url(session('role') . '/schedule/saveScheduleSample') ?>" method="post">
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
                                            <option value="BENANG">BENANG</option>
                                            <option value="NYLON">NYLON</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <!-- Kode Warna -->
                                    <div class="mb-3">
                                        <label for="kode_warna" class="form-label">Kode Warna</label>
                                        <input type="text" class="form-control" id="kode_warna" name="kode_warna" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <!-- Warna -->
                                    <div class="mb-3">
                                        <label for="warna" class="form-label">Warna</label>
                                        <input type="text" class="form-control" id="warna" name="warna" required>

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
                                                                    <input type="text" class="form-control" id="item_type[]" name="item_type[]" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="po">PO</label>
                                                                    <input type="text" class="form-control" id="po[]" name="po[]" required>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <div class="form-group">
                                                                    <label for="tgl_start_mc">Tgl Start MC</label>
                                                                    <input type="date" class="form-control" name="tgl_start_mc[]">
                                                                </div>
                                                            </div>

                                                            <div class="col-4">
                                                                <div class="form-group">
                                                                    <label for="delivery_awal">Delivery Awal</label>
                                                                    <input type="date" class="form-control" name="delivery_awal[]">
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="form-group ">
                                                                    <label for="delivery_akhir">Delivery Akhir</label>
                                                                    <input type="date" class="form-control" name="delivery_akhir[]">
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <div class="form-group">
                                                                    <label for="qty_po">Qty PO</label>
                                                                    <input type="number" class="form-control" name="qty_po[]">
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="form-group">
                                                                    <label for="qty_po_plus">Qty PO (+)</label>
                                                                    <input type="number" class="form-control" name="qty_po_plus[]">
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
                                                            <div class="col-3">
                                                                <div class="form-group">
                                                                    <label for="qty_celup">Stock :</label>
                                                                    <br />
                                                                    <span class="badge bg-info">
                                                                        <span class="stock">0.00</span> KG <!-- Ganti id dengan class -->
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-3">
                                                                <div class="form-group">
                                                                    <label for="qty_celup">KG Kebutuhan :</label>
                                                                    <br />
                                                                    <span class="badge bg-info">
                                                                        <span class="kg_kebutuhan">0.00</span> KG <!-- Ganti id dengan class -->
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-3">
                                                                <div class="form-group">
                                                                    <label for="tagihan">Tagihan :</label>
                                                                    <br />
                                                                    <span class="badge bg-info">
                                                                        <span class="tagihan">0.00</span> KG
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-3 d-flex align-items-center">
                                                                <div class="form-group">
                                                                    <label for="qty_celup">PO + :</label>
                                                                    <fieldset>
                                                                        <legend></legend>
                                                                        <div>
                                                                            <input type="radio" id="po_plus" name="po_plus[]" value="1">
                                                                            <label for="iya">Iya</label>
                                                                            <input type="radio" id="po_plus" name="po_plus[]" value="0">
                                                                            <label for="tidak">Tidak</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="">Keterangan PO</label>
                                                                    <br />
                                                                    <textarea class="form-control keterangan" name="keterangan" id="keterangan" disabled></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="">Keterangan Schedule</label>
                                                                    <br />
                                                                    <textarea class="form-control ket_schedule[]" name="ket_schedule[]" id="ket_schedule"></textarea>
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
        // const warnaInput = document.getElementById('warna'); // Input untuk menampilkan warna
        const warnaSelect = document.getElementById('warna');
        const poTable = document.getElementById("poTable");
        // Variabel untuk debounce dan flag ketika saran dipilih
        let debounceTimer;
        let suggestionSelected = false;

        // === Event Listener untuk input kode warna dan saran ===
        kodeWarna.addEventListener('input', function() {
            if (suggestionSelected) {
                suggestionSelected = false;
                return;
            }
            clearTimeout(debounceTimer);
            const query = kodeWarna.value;
            debounceTimer = setTimeout(() => {
                fetchKodeWarnaSuggestions(query);
            }, 300);
        });

        function fetchKodeWarnaSuggestions(query) {
            if (query.length < 2) {
                suggestionsBoxKWarna.style.display = 'none';
                return;
            }
            fetch('<?= base_url(session('role') . "/schedule/getKodeWarna") ?>?query=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    const kodeWarnaSuggestions = data.map(item => item.kode_warna);
                    displayKodeWarnaSuggestions(kodeWarnaSuggestions);
                })
                .catch(error => {
                    console.error('Error fetching kode warna suggestions:', error);
                });
        }

        function displayKodeWarnaSuggestions(suggestions) {
            suggestionsBoxKWarna.innerHTML = '';
            if (suggestions.length > 0) {
                suggestionsBoxKWarna.style.display = 'block';
                suggestions.forEach(suggestion => {
                    const suggestionDiv = document.createElement('div');
                    suggestionDiv.textContent = suggestion;
                    suggestionDiv.addEventListener('click', function() {
                        suggestionSelected = true;
                        kodeWarna.value = suggestion;
                        suggestionsBoxKWarna.style.display = 'none';
                        loadWarnaOptions(suggestion);
                    });
                    suggestionsBoxKWarna.appendChild(suggestionDiv);
                });
            } else {
                suggestionsBoxKWarna.style.display = 'none';
            }
        }

        // function fetchWarnaByKodeWarna(kodeWarnaValue) {
        //     fetch('<?= base_url(session('role') . "/schedule/getWarna") ?>?kode_warna=' + encodeURIComponent(kodeWarnaValue))
        //         .then(response => response.json())
        //         .then(data => {
        //             if (data.length > 0) {
        //                 warnaInput.value = data[0].color;
        //                 fetchItemType(kodeWarnaValue, data[0].color);
        //             } else {
        //                 warnaInput.value = 'Warna tidak ditemukan';
        //             }
        //         })
        //         .catch(error => {
        //             console.error('Error fetching warna by kode warna:', error);
        //             warnaInput.value = 'Error mengambil warna';
        //         });
        // }

        // Fetch warna list by kode warna
        function loadWarnaOptions(kode) {
            fetch('<?= base_url(session('role') . "/schedule/getWarna") ?>?kode_warna=' + encodeURIComponent(kode))
                .then(res => res.json())
                .then(data => {
                    warnaSelect.innerHTML = '<option value="">Pilih Warna</option>';
                    if (data.length) {
                        data.forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.color;
                            opt.textContent = item.color;
                            opt.dataset.idInduk = item.id_induk || '';
                            warnaSelect.appendChild(opt);
                        });
                    } else {
                        warnaSelect.innerHTML += '<option value="">Tidak ada warna</option>';
                    }
                })
                .catch(err => console.error(err));
        }

        // On warna change, fetch item type
        warnaSelect.addEventListener('change', function() {
            const selected = warnaSelect.options[warnaSelect.selectedIndex];
            const color = selected.value;
            const idInduk = selected.dataset.idInduk;
            if (color) {
                fetchItemType(kodeWarna.value, color, idInduk);
            }
        });
        // === Fungsi untuk mengambil dan mengisi Item Type ===
        function fetchItemType(kodeWarna, warna) {
            fetch(`<?= base_url(session('role') . "/schedule/getItemType") ?>?kode_warna=${kodeWarna}&warna=${warna}`)
                .then(response => response.json())
                .then(data => {
                    const itemType = document.querySelector(".item-type");
                    if (data.length > 0) {
                        itemType.innerHTML = '<option value="">Pilih Item Type</option>';
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.item_type;
                            option.textContent = item.item_type;
                            option.setAttribute("data-id-induk", item.id_induk);
                            itemType.appendChild(option);
                        });
                        // Event ketika Item Type berubah
                        $(itemType).on('change', function() {
                            const selectedOption = itemType.options[itemType.selectedIndex];
                            const tr = itemType.closest("tr");
                            const kodeWarnaVal = document.querySelector("input[name='kode_warna']").value;
                            const warnaVal = document.querySelector("select[name^='warna']").value;
                            const idInduk = selectedOption.getAttribute("data-id-induk") || 0;
                            if (selectedOption.value) {
                                fetchPOByKodeWarna(kodeWarnaVal, tr, warnaVal, selectedOption.value, idInduk, tr.querySelector("select[name='po[]']"));
                                fetchStock(kodeWarnaVal, tr, warnaVal, selectedOption.value);
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

        // === Fungsi untuk mengambil data PO berdasarkan kode warna, item type, dsb ===
        function fetchPOByKodeWarna(kodeWarna, tr, warna, itemType, idInduk, poSelect) {
            const itemTypeEncoded = encodeURIComponent(itemType);
            idInduk = idInduk || 0;
            const url = `<?= base_url(session('role') . "/schedule/getPO") ?>?kode_warna=${kodeWarna}&warna=${warna}&item_type=${itemTypeEncoded}&id_induk=${idInduk}`;
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    console.log("PO Data:", data);
                    if (Array.isArray(data) && data.length > 0) {
                        poSelect.innerHTML = '<option value="">Pilih PO</option>';
                        data.forEach(po => {
                            const option = document.createElement('option');
                            option.value = po.no_model;
                            option.textContent = po.no_model;
                            poSelect.appendChild(option);
                            // const tglStartMC = tr.querySelector("input[name='tgl_start_mc[]']");
                            const deliveryAwal = tr.querySelector("input[name='delivery_awal[]']");
                            const deliveryAkhir = tr.querySelector("input[name='delivery_akhir[]']");
                            // Update data schedule dan qty_po tanpa cek kondisi (selalu gunakan data terbaru)
                            // tglStartMC.value = po.start_mesin || '';
                            deliveryAwal.value = po.delivery_awal || '';
                            deliveryAkhir.value = po.delivery_akhir || '';

                        });

                    } else {
                        poSelect.innerHTML = '<option value="">Tidak ada PO</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching PO data:', error);
                    poSelect.innerHTML = '<option value="">Gagal mengambil PO</option>';
                });
        }

        // === Fungsi untuk mengambil data Qty dan Kebutuhan PO (selain qty_po) ===
        function fetchQtyAndKebutuhanPO(kodeWarna, tr, warna, itemType) {
            const itemTypeEncoded = encodeURIComponent(itemType);
            // idInduk = idInduk || 0;
            const url = `<?= base_url(session('role') . "/schedule/getQtyPO") ?>?kode_warna=${kodeWarna}&color=${warna}&item_type=${itemTypeEncoded}`;
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data && !data.error) {
                        // Hanya update field tambahan (qty_po_plus, KG Kebutuhan dan Sisa Jatah)
                        const qtyPO = tr.querySelector("input[name='qty_po[]']");
                        const qtyPOPlus = tr.querySelector("input[name='qty_po_plus[]']");
                        const kgKebutuhan = tr.querySelector(".kg_kebutuhan");
                        const tagihan = tr.querySelector(".tagihan");
                        qtyPO.value = parseFloat(data.kg_po).toFixed(2);
                        qtyPOPlus.value = parseFloat(data.qty_po_plus).toFixed(2) || '';
                        kgKebutuhan.textContent = parseFloat(data.kg_po).toFixed(2) || '0.00';
                        tagihan.textContent = parseFloat(data.sisa_kg_po).toFixed(2) || '0.00';
                    } else {
                        console.error('Error fetching PO details:', data.error || 'No data found');
                    }
                })
                .catch(error => {
                    console.error('Error fetching Qty data:', error);
                });
        }

        // === Fungsi untuk mengambil detail PO (schedule & qty_po) ===
        function fetchPODetails(poNo, tr, itemType, kodeWarna) {
            const url = `<?= base_url(session('role') . "/schedule/getPODetails") ?>?no_model=${poNo}&item_type=${encodeURIComponent(itemType)}&kode_warna=${encodeURIComponent(kodeWarna)}`;
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data && !data.error) {
                        const tglStartMC = tr.querySelector("input[name='tgl_start_mc[]']");
                        const deliveryAwal = tr.querySelector("input[name='delivery_awal[]']");
                        if (deliveryAwal) {
                            deliveryAwal.value = data.delivery_awal || '';
                        } else {
                            console.error("Input delivery_awal[] not found in row:", tr);
                        }

                        tr.querySelector("input[name='delivery_akhir[]']").value = data.delivery_akhir || '';
                    } else {
                        console.error('Error fetching PO details:', data.error || 'No data found');
                    }
                })
                .catch(error => {
                    console.error('Error fetching PO details:', error);
                });
        }

        //Cek Stok
        function fetchStock(kodeWarna, tr, warna, itemType) {
            const kodeWarnaEnc = encodeURIComponent(kodeWarna);
            const warnaEnc = encodeURIComponent(warna);
            const itemTypeEncoded = encodeURIComponent(itemType);
            const url = `<?= base_url(session('role') . "/schedule/getStock") ?>?kode_warna=${kodeWarnaEnc}&color=${warnaEnc}&item_type=${itemTypeEncoded}`;
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data && !data.error) {

                        const stock = tr.querySelector(".stock");
                        stock.textContent = isNaN(stok) ? '0.00' : stok.toFixed(2);
                    } else {
                        console.error('Error fetching Stock:', data.error || 'No data found');
                    }
                })
                .catch(error => {
                    console.error('Error fetching Stock:', error);
                });
        }

        //Keterangan dari Open PO
        function fetchKeterangan(kodeWarna, tr, warna, itemType, noModel) {
            const kodeWarnaEnc = encodeURIComponent(kodeWarna);
            const warnaEnc = encodeURIComponent(warna);
            const itemTypeEncoded = encodeURIComponent(itemType);
            const noModelEncoded = encodeURIComponent(noModel);
            const url = `<?= base_url(session('role') . "/schedule/getKeterangan") ?>?kode_warna=${kodeWarnaEnc}&color=${warnaEnc}&item_type=${itemTypeEncoded}&no_model=${noModelEncoded}`;
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data && !data.error) {
                        const ketPo = tr.querySelector("textarea.keterangan");
                        if (ketPo) {
                            ketPo.value = data.keterangan || '';
                        }
                    } else {
                        console.error('Error fetching Keterangan:', data.error || 'No data found');
                    }
                })
                .catch(error => {
                    console.error('Error fetching Keterangan:', error);
                });
        }

        // Fungsi untuk menghitung sisa kapasitas
        function calculateRemainingCapacity() {
            const maxCaps = parseFloat(document.getElementById("max_caps").value) || 0;
            let totalQtyCelup = 0;

            document.querySelectorAll("input[name='qty_celup[]']").forEach(input => {
                totalQtyCelup += parseFloat(input.value) || 0;
            });

            const sisaKapasitas = maxCaps - totalQtyCelup;
            document.getElementById("sisa_kapasitas").value = sisaKapasitas.toFixed(2);

            // Update tampilan berdasarkan kondisi
            const sisaInput = document.getElementById("sisa_kapasitas");
            if (sisaKapasitas < 0) {
                sisaInput.classList.add("is-invalid");
            } else {
                sisaInput.classList.remove("is-invalid");
            }
        }

        // Fungsi untuk validasi input qty_celup
        function validateQtyCelup(input) {
            const maxCaps = parseFloat(document.getElementById("max_caps").value);
            const currentValue = parseFloat(input.value) || 0;

            if (currentValue < 0.01) {
                input.setCustomValidity("Qty Celup minimal 0.01");
            } else if (currentValue > maxCaps) {
                input.setCustomValidity("Qty Celup melebihi kapasitas maksimal");
            } else {
                input.setCustomValidity("");
            }
        }

        // Event listener untuk input qty_celup
        document.addEventListener("input", function(e) {
            if (e.target.name === "qty_celup[]") {
                validateQtyCelup(e.target);
                calculateRemainingCapacity();
            }
        });

        // Event listener untuk perubahan struktur tabel
        document.addEventListener("DOMNodeInserted", function(e) {
            if (e.target.classList.contains("removeRow")) {
                calculateRemainingCapacity();
            }
        });

        // Inisialisasi awal saat halaman dimuat
        document.addEventListener("DOMContentLoaded", function() {
            calculateRemainingCapacity();

            // Tambahkan event listener untuk semua input yang ada
            document.querySelectorAll("input[name='qty_celup[]']").forEach(input => {
                input.addEventListener("input", function() {
                    validateQtyCelup(this);
                    calculateRemainingCapacity();
                });
            });
        });

        // === Fungsi untuk menghitung total Qty Celup dan sisa kapasitas ===
        function calculateTotalAndRemainingCapacity() {
            const qtyCelupInputs = document.querySelectorAll("input[name='qty_celup[]']");
            let totalQtyCelup = 0;

            qtyCelupInputs.forEach(input => {
                totalQtyCelup += parseFloat(input.value) || 0;
            });

            const totalQtyCelupElement = document.getElementById("total_qty_celup");
            if (totalQtyCelupElement) {
                totalQtyCelupElement.value = totalQtyCelup.toFixed(2);
            }

            const maxCaps = parseFloat(document.getElementById("max_caps").value) || 0;

            if (totalQtyCelup > maxCaps) {
                Swal.fire({
                    icon: "warning",
                    title: "Peringatan!",
                    text: "Total Qty Celup melebihi Max Caps!",
                    confirmButtonColor: "#d33"
                }).then(() => {
                    totalQtyCelupElement.classList.add("is-invalid");
                    totalQtyCelupElement.focus();
                    totalQtyCelupElement.value = "0.00";
                    totalQtyCelup = 0; // Reset totalQtyCelup jika diperlukan

                    qtyCelupInputs.forEach(input => {
                        input.value = "0.00";
                        input.classList.add("is-invalid");
                    });
                });
            } else {
                totalQtyCelupElement.value = totalQtyCelup.toFixed(2);
                totalQtyCelupElement.classList.remove("is-invalid");
            }

            const rows = poTable.querySelectorAll("tbody tr");

            rows.forEach(row => {
                const inputCelup = row.querySelector("input[name='qty_celup[]']");
                const qtyCelup = parseFloat(inputCelup.value) || 0;
                const tagihanSCH = parseFloat(row.querySelector(".sisa_jatah").textContent) || 0;

                if (qtyCelup > tagihanSCH) {
                    Swal.fire({
                        icon: "warning",
                        title: "Peringatan!",
                        text: `Qty Celup di baris ini melebihi Tagihan SCH! (Tagihan SCH: ${tagihanSCH.toFixed(2)})`,
                        confirmButtonColor: "#d33"
                    }).then(() => {
                        inputCelup.classList.add("is-invalid");
                        inputCelup.focus();
                        inputCelup.value = "0.00"; // Reset input qty celup di baris tersebut
                    });
                } else {
                    inputCelup.classList.remove("is-invalid");
                }
            });

            // Hitung dan set sisa kapasitas
            const sisaKapasitasElement = document.getElementById("sisa_kapasitas");
            if (sisaKapasitasElement) {
                const sisaKapasitas = maxCaps - totalQtyCelup;
                sisaKapasitasElement.value = sisaKapasitas.toFixed(2);

                if (sisaKapasitas < 0) {
                    Swal.fire({
                        icon: "warning",
                        title: "Peringatan!",
                        text: "Sisa Kapasitas negatif!",
                        confirmButtonColor: "#d33"
                    }).then(() => {
                        sisaKapasitasElement.classList.add("is-invalid");
                        sisaKapasitasElement.focus();
                    });
                } else {
                    sisaKapasitasElement.classList.remove("is-invalid");
                }
            }
        }

        poTable.addEventListener("input", function(e) {
            if (e.target.name === "qty_celup[]") {
                calculateTotalAndRemainingCapacity();
            }
        });

        // === Event Listener saat user memilih PO ===
        poTable.addEventListener("change", function(e) {
            if (e.target.classList.contains("po-select")) {
                const poSelect = e.target;
                const selectedOption = poSelect.options[poSelect.selectedIndex];
                const tr = poSelect.closest("tr");
                const itemTypeValue = tr.querySelector("select[name^='item_type']").value;
                const kodeWarnaValue = document.querySelector("input[name='kode_warna']").value;
                const warna = document.querySelector("select[name^='warna']").value;
                const idIndukValue = tr.querySelector("select[name^='item_type']").selectedOptions[0].getAttribute("data-id-induk") || 0;
                const noModelValue = selectedOption.value;

                // Reset qty_po dan KG Kebutuhan ke 0.00 saat terjadi perubahan PO
                const qtyPO = tr.querySelector("input[name='qty_po[]']");
                const kgKebutuhanElement = tr.querySelector(".kg_kebutuhan");
                qtyPO.value = "0.00";
                kgKebutuhanElement.textContent = "0.00";

                if (!itemTypeValue || !kodeWarnaValue) {
                    console.error("Item Type atau Kode Warna tidak boleh kosong.");
                    return;
                }
                if (selectedOption.value) {
                    fetchQtyAndKebutuhanPO(kodeWarnaValue, tr, warna, itemTypeValue);
                    fetchKeterangan(kodeWarnaValue, tr, warna, itemTypeValue, noModelValue);
                    fetchPODetails(selectedOption.value, tr, itemTypeValue, kodeWarnaValue);
                } else {
                    // Reset schedule jika PO kosong
                    const tglStartMC = tr.querySelector("input[name='tgl_start_mc[]']");
                    const deliveryAwal = tr.querySelector("input[name='delivery_awal[]']");
                    const deliveryAkhir = tr.querySelector("input[name='delivery_akhir[]']");
                    tglStartMC.value = '';
                    deliveryAwal.value = '';
                    deliveryAkhir.value = '';
                }
            }
        });


        // === Menambahkan baris baru ===
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
                                                                    <input type="text" class="form-control" id="item_type[]" name="item_type[]" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="po">PO</label>
                                                                    <input type="text" class="form-control" id="po[]" name="po[]" required>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <div class="form-group">
                                                                    <label for="tgl_start_mc">Tgl Start MC</label>
                                                                    <input type="date" class="form-control" name="tgl_start_mc[]">
                                                                </div>
                                                            </div>

                                                            <div class="col-4">
                                                                <div class="form-group">
                                                                    <label for="delivery_awal">Delivery Awal</label>
                                                                    <input type="date" class="form-control" name="delivery_awal[]">
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="form-group ">
                                                                    <label for="delivery_akhir">Delivery Akhir</label>
                                                                    <input type="date" class="form-control" name="delivery_akhir[]">
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
                <div class="col-3">
                    <div class="form-group">
                        <label for="qty_celup">Stock</label>
                        <br />
                            <span class="badge bg-info">
                            <span class="stock">0.00</span> KG <!-- Ganti id dengan class -->
                            </span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="qty_celup">KG Kebutuhan :</label>
                            <br />
                            <span class="badge bg-info">
                                <span class="kg_kebutuhan">0.00</span> KG
                            </span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="tagihan">Tagihan :</label>
                            <br />
                            <span class="badge bg-info">
                                <span class="tagihan">0.00</span> KG
                            </span>
                        </div>
                    </div>
                    <div class="col-3 d-flex align-items-center">
                        <div class="form-group">
                            <label for="qty_celup">PO + :</label>
                            <fieldset>
                                <legend></legend>
                                <div>
                                    <input type="radio" id="po_plus" name="po_plus[]" value="1">
                                    <label for="iya">Iya</label>
                                    <input type="radio" id="po_plus" name="po_plus[]" value="0">
                                    <label for="tidak">Tidak</label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">Keterangan</label>
                            <br />
                            <textarea class="form-control keterangan" name="keterangan" id="keterangan" readonly></textarea>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">Keterangan Schedule</label>
                            <br />
                            <textarea class="form-control ket_schedule[]" name="ket_schedule[]" id="ket_schedule"></textarea>
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

            // Isi opsi item_type di baris baru
            const itemTypeSelect = newRow.querySelector(".item-type");
            fetchItemTypeRow(kodeWarna.value, warnaSelect.value, itemTypeSelect);

            $(itemTypeSelect).on('change', function() {
                const itemTypeValue = $(this).val();
                const poSelect = newRow.querySelector(".po-select");
                const idIndukValue = $(this).find(':selected').data('id-induk') || 0;

                fetchPOByKodeWarna(kodeWarna.value, newRow, warnaSelect.value, itemTypeValue, idIndukValue, poSelect);
                fetchStock(kodeWarna.value, newRow, warnaSelect.value, itemTypeValue);
                fetchKeterangan(kodeWarna.value, newRow, warnaSelect.value, itemTypeValue, poSelect);
                // fetchQtyAndKebutuhanPO(kodeWarna.value, newRow, warnaSelect.value, itemTypeValue, idIndukValue);
            });

            newRow.querySelector("input[name='qty_celup[]']").addEventListener("input", function() {
                calculateTotalAndRemainingCapacity();
            });
        });

        function fetchItemTypeRow(kodeWarna, warna, itemTypeSelect) {
            fetch(`<?= base_url(session('role') . "/schedule/getItemType") ?>?kode_warna=${kodeWarna}&warna=${warna}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        itemTypeSelect.innerHTML = '<option value="">Pilih Item Type</option>';
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.item_type;
                            option.textContent = item.item_type;
                            option.setAttribute("data-id-induk", item.id_induk);
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

        poTable.addEventListener("click", function(e) {
            if (e.target.classList.contains("removeRow")) {
                e.target.closest("tr").remove();
                calculateTotalAndRemainingCapacity();
            }
        });
    });
</script>


<?php $this->endSection(); ?>