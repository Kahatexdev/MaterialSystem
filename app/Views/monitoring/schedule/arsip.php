<script>
    document.addEventListener("DOMContentLoaded", function() {
        const itemType = document.getElementById("item_type");
        const kodeWarna = document.getElementById("kode_warna");
        const poTable = document.getElementById("poTable");

        // 1. Optimasi Event Handling untuk Status Row
        document.querySelectorAll('tr[data-status]').forEach(row => {
            const status = row.getAttribute('data-status');
            if (['celup', 'done', 'sent'].includes(status)) {
                row.querySelectorAll('input, select, button').forEach(input => {
                    input.disabled = true;
                    input.readOnly = true;
                });
            }
        });

        // 2. Event Delegation untuk Tombol Edit
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('editRow')) {
                const button = e.target;
                const idCelup = button.dataset.id;
                const modal = new bootstrap.Modal(document.getElementById('editModal'));

                document.getElementById('idCelup').value = idCelup;
                document.getElementById('tanggal_schedule').value = button.dataset.tanggalschedule;

                modal.show();
            }
        });

        // 3. Ajax Submit dengan Error Handling
        $('#editModal form').submit(function(e) {
            e.preventDefault();
            const formData = {
                id_celup: $('#idCelup').val(),
                tanggal_schedule: $('#tanggal_schedule').val(),
                no_mesin: $('#no_mesin').val(),
                lot_urut: $('#lot_urut').val()
            };

            $.ajax({
                    url: '<?= base_url(session('role') . '/schedule/updateTglSchedule') ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json'
                })
                .done(response => {
                    if (response.success) {
                        alert('Update berhasil!');
                        location.reload();
                    } else {
                        alert('Gagal: ' + (response.message || 'Terjadi kesalahan'));
                    }
                })
                .fail((xhr, status, error) => {
                    alert(`Error: ${error}`);
                    console.error('Detail error:', xhr.responseText);
                });
        });

        // 4. Optimasi PO Dropdown dengan Event Delegation
        poTable.addEventListener('change', function(e) {
            if (e.target.classList.contains('po-select')) {
                const row = e.target.closest('tr');
                const poId = e.target.value;
                const itemTypeValue = itemType.value;
                const kodeWarnaValue = kodeWarna.value;

                if (!poId) {
                    resetRowInputs(row);
                    return;
                }

                fetchPODetails(row, poId, itemTypeValue, kodeWarnaValue);
                fetchQtyPO(row, poId, itemTypeValue, kodeWarnaValue);
            }
        });

        // Fungsi Bantuan
        function resetRowInputs(row) {
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => input.value = '');
        }

        async function fetchPODetails(row, poId, itemType, kodeWarna) {
            try {
                const response = await $.ajax({
                    url: '<?= base_url(session("role") . "/schedule/getPODetails") ?>',
                    type: 'GET',
                    data: {
                        id_order: poId,
                        itemType,
                        kodeWarna
                    }
                });

                const kgKebutuhan = parseFloat(response.kg_kebutuhan || 0);
                const qtyCelup = parseFloat(row.querySelector('[name^="qty_celup["]').value) || 0;

                row.querySelector('.start_mc').value = response.start_mesin || '';
                row.querySelector('.delivery_awal').value = response.delivery_awal || '';
                row.querySelector('.delivery_akhir').value = response.delivery_akhir || '';
                row.querySelector('.kg_kebutuhan').value = kgKebutuhan.toFixed(2);
                row.querySelector('.tagihan_schedule').value = (kgKebutuhan - qtyCelup).toFixed(2);

            } catch (error) {
                console.error("Error fetching PO details:", error);
                alert('Gagal memuat detail PO');
            }
        }

        async function fetchQtyPO(row, poId, itemType, kodeWarna) {
            try {
                const response = await $.ajax({
                    url: '<?= base_url(session("role") . "/schedule/getQtyPO") ?>',
                    type: 'GET',
                    data: {
                        id_order: poId,
                        item_type: itemType,
                        kode_warna: kodeWarna
                    }
                });

                if (response) {
                    row.querySelector('.qty_po').value = parseFloat(response.kgs || 0).toFixed(2);
                }
            } catch (error) {
                console.error("Error fetching Qty PO:", error);
                alert('Gagal memuat Qty PO');
            }
        }


        // Hitung sisa kapasitas
        function calculateCapacity() {
            const max = parseFloat(maxCaps.value) || 0;
            let total = 0;

            const rows = poTable.querySelectorAll("tbody tr");

            rows.forEach((row, index) => {
                const qtyCelupInput = row.querySelector('input[name^="qty_celup["]');
                const lastStatusInput = row.querySelector('input[name^="last_status["]');

                if (qtyCelupInput && lastStatusInput) {
                    const qtyCelup = parseFloat(qtyCelupInput.value) || 0;
                    const lastStatus = lastStatusInput.value;

                    if (lastStatus === 'scheduled' || lastStatus === 'celup' || lastStatus === 'reschedule') {
                        total += qtyCelup;
                    }
                } else {
                    console.warn(`Input qty_celup atau last_status tidak ditemukan di baris ke-${index}`);
                }
            });

            totalQtyCelup.value = total.toFixed(2); // Total qty celup
            const sisa = max - total;
            sisaKapasitas.value = sisa.toFixed(2); // Sisa kapasitas

            // Debugging log
            console.log(`Max Capacity: ${max}, Total: ${total}, Remaining: ${sisa}`);

            // Jika sisa kapasitas < 0, beri peringatan
            if (sisa < 0) {
                alert('Sisa kapasitas tidak mencukupi!');
            }

            validateQtyCelup(max); // Validasi untuk qty celup
            validateSisaJatah(); // Validasi sisa jatah
        }

        // Validasi qty_celup untuk setiap input
        function validateQtyCelup(max) {
            const qtyCelupInputs = document.querySelectorAll('input[name="qty_celup[]"]');
            let isOverCapacity = false;

            qtyCelupInputs.forEach(input => {
                const kg = parseFloat(input.value) || 0;

                if (kg > max) {
                    input.setCustomValidity(`Qty Celup Melebihi Kapasitas ${max}`);
                    isOverCapacity = true;
                } else if (parseFloat(sisaKapasitas.value) < 0) {
                    input.setCustomValidity(`Sisa Kapasitas Tidak Mencukupi`);
                    isOverCapacity = true;
                } else {
                    input.setCustomValidity('');
                }
            });

            // Jika over capacity, tampilkan pesan
            if (isOverCapacity) {
                alert('Sisa kapasitas tidak mencukupi atau melebihi kapasitas maksimum.');
            }
        }

        // Event Listener untuk Input `qty_celup`
        poTable.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty_celup')) {
                const row = e.target.closest('tr');
                validateSisaJatah(); // Validasi sisa jatah setelah input qty_celup
                calculateCapacity(); // Hitung ulang kapasitas setelah validasi
            }
        });

        function validateSisaJatah() {
            const rows = poTable.querySelectorAll("tbody tr");
            let isValid = true;

            // Collect all necessary data to send in one request
            let requestData = [];

            rows.forEach((row) => {
                const noModelInput = row.querySelector('input[name^="po["]');
                const itemTypeInput = row.querySelector('input[name^="item_type["]');
                const kodeWarnaInput = row.querySelector('input[name^="kode_warna["]');
                const qtyCelupInput = row.querySelector('input[name^="qty_celup["]');
                const currentQtyCelupInput = row.querySelector('input[name^="current_qty_celup["]'); // Hidden input

                if (noModelInput && itemTypeInput && kodeWarnaInput && qtyCelupInput) {
                    const noModel = noModelInput.value;
                    const itemType = itemTypeInput.value;
                    const kodeWarna = kodeWarnaInput.value;
                    const qtyCelup = parseFloat(qtyCelupInput.value) || 0;
                    const currentQtyCelup = parseFloat(currentQtyCelupInput?.value) || 0;

                    // Collect the data to send
                    requestData.push({
                        no_model: noModel,
                        item_type: itemType,
                        kode_warna: kodeWarna,
                        qty_celup: qtyCelup ?? 0,
                        current_qty_celup: currentQtyCelup
                    });
                } else {
                    console.warn('Data tidak lengkap untuk validasi.', row);
                    isValid = false;
                }
            });

            // if (!isValid) {
            //     alert('Ada baris data yang tidak lengkap untuk validasi.');
            //     return;
            // }

            // Send a single AJAX request for all rows
            $.ajax({
                url: '<?= base_url("/schedule/validateSisaJatah") ?>',
                type: 'POST',
                data: {
                    rows: requestData
                },
                dataType: 'json',
                success: function(response) {
                    if (!response.success) {
                        // Show validation error for each row
                        response.errors.forEach((error, index) => {
                            const qtyCelupInput = rows[index].querySelector('input[name^="qty_celup["]');
                            qtyCelupInput.setCustomValidity(error.message);
                            alert(error.message);
                        });
                    } else {
                        // If successful, reset custom validity for all rows
                        rows.forEach((row) => {
                            const qtyCelupInput = row.querySelector('input[name^="qty_celup["]');
                            qtyCelupInput.setCustomValidity('');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memvalidasi sisa jatah.');
                }
            });
        }




        // Event listener untuk menambah baris baru
        document.getElementById("addRow").addEventListener("click", function() {
            const tbody = document.querySelector("#poTable tbody");
            const newIndex = tbody.querySelectorAll("tr").length;

            const newRow = `
        <tr>
            <td>
                <select class="form-select po-select" name="po-select[${newIndex}]" required>
                    <option value="">Pilih PO</option>
                    <?php foreach ($po as $option): ?>
                        <option value="<?= $option['id_order'] ?>"><?= $option['no_model'] ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="po[${newIndex}]" value="">
            </td>
            <td><input type="date" class="form-control start_mc" name="start_mc[${newIndex}]" readonly></td>
            <td><input type="date" class="form-control delivery_awal" name="delivery_awal[${newIndex}]" readonly></td>
            <td><input type="date" class="form-control delivery_akhir" name="delivery_akhir[${newIndex}]" readonly></td>
            <td><input type="number" class="form-control qty_po" name="qty_po[${newIndex}]" readonly></td>
            <td><input type="number" class="form-control qty_po_plus" name="qty_po_plus[${newIndex}]" required readonly></td>
            <td><input type="number" class="form-control kg_kebutuhan" name="kg_kebutuhan[${newIndex}]" readonly></td>
            <td><input type="number" class="form-control tagihan_schedule" name="tagihan_schedule[${newIndex}]" readonly></td>
            <td><input type="number" step="0.01" class="form-control qty_celup" name="qty_celup[${newIndex}]" required></td>
            <td>
                <select class="form-select po_plus" name="po_plus[${newIndex}]" required>
                    <option value="">Pilih PO(+)</option>
                    <option value="1">Iya</option>
                    <option value="0">Bukan</option>
                </select>
            </td>
            <td>
                <span class="badge bg-info">scheduled</span>
                <input type="hidden" class="form-control last_status" name="last_status[${newIndex}]" value="scheduled" required readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger removeRow">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;

            tbody.insertAdjacentHTML("beforeend", newRow);

            // Ambil elemen select yang baru ditambahkan
            const newSelect = tbody.querySelector(`select[name="po-select[${newIndex}]"]`);

            // Event listener untuk mengatur PO
            newSelect.addEventListener('change', function() {
                const selectedValue = newSelect.value; // Ambil value yang dipilih

                // Ambil `no_model` berdasarkan `id_order`
                $.ajax({
                    url: '<?= base_url(session("role") . "/schedule/getNoModel") ?>',
                    type: 'GET',
                    data: {
                        id_order: selectedValue
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('No Model:', response.no_model);
                        newSelect.nextElementSibling.value = response.no_model;
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching No Model:", error);
                    },
                });
            });

            // Perbarui dropdown dan event listener pada baris baru
            calculateCapacity();
            updatePODropdown();
        });


        // Event listener untuk menghapus baris
        document.querySelector("#poTable").addEventListener("click", function(event) {
            if (event.target.closest(".removeRow")) {
                const row = event.target.closest("tr");

                // Validasi jika ada input ID Celup pada baris
                const idCelupInput = row.querySelector('input[name^="id_celup["]');
                const idCelup = idCelupInput ? idCelupInput.value : null;

                if (idCelup) {
                    // SweetAlert konfirmasi sebelum menghapus
                    Swal.fire({
                        title: "Apakah Anda Yakin?",
                        text: "Data Schedule akan dihapus",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Batal",
                        dangerMode: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Kirim permintaan AJAX untuk menghapus data
                            $.ajax({
                                url: '<?= base_url(session('role') . '/schedule/deleteSchedule') ?>',
                                type: 'POST',
                                data: {
                                    id_celup: idCelup
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire("Berhasil!", "Data Schedule berhasil dihapus.", "success").then(() => {
                                            row.remove();
                                            calculateCapacity();
                                        });
                                    } else {
                                        Swal.fire("Gagal!", "Data Schedule gagal dihapus.", "error");
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
                                },
                            });
                        }
                    });
                } else {
                    // Jika id_celup tidak ditemukan atau null, cukup hapus barisnya saja
                    Swal.fire({
                        title: "Hapus Baris?",
                        text: "Baris ini akan dihapus tanpa mengirim data ke server.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Batal",
                        dangerMode: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            row.remove();
                            calculateCapacity();
                        }
                    });
                }
            }
        });

        // Panggil fungsi untuk memperbarui dropdown PO dan event listener pada baris yang sudah ada
        updatePODropdown();
        calculateCapacity();
    });
</script>