<?php $this->extend($role . '/god/header'); ?>
<?php $this->section('content'); ?>

<style>
    /* Custom styles for the import form */
    .drag-drop-area {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        background-color: #f8fafc;
        cursor: pointer;
        position: relative;
    }

    .drag-drop-area:hover {
        border-color: #94a3b8;
        background-color: #f1f5f9;
    }

    .drag-drop-area.dragover {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }

    .drag-drop-area.file-selected {
        border-color: #10b981;
        background-color: #f0fdf4;
    }

    .file-input-hidden {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .gradient-header {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    }

    .btn-gradient {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none;
        transition: all 0.3s ease;
    }

    .btn-gradient:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
    }

    .btn-gradient:disabled {
        background: #94a3b8;
        transform: none;
        box-shadow: none;
    }

    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        max-width: 400px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .spinner {
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .file-icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 1rem;
        color: #64748b;
    }

    .requirements-card {
        background-color: #fefbf3;
        border: 1px solid #fbbf24;
        border-radius: 8px;
    }

    .help-section {
        background-color: #f8fafc;
        border-radius: 8px;
    }

    .sample-data {
        background-color: #f1f5f9;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
    }

    .process-step {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .process-dot {
        width: 8px;
        height: 8px;
        background-color: #3b82f6;
        border-radius: 50%;
        margin-right: 0.5rem;
    }
</style>

<div class="container-fluid py-4" style="min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
    <!-- Notifications -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="notification alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                <div class="flex-grow-1">
                    <strong>Success!</strong><br>
                    <?= session()->getFlashdata('success') ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="notification alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-exclamation-circle text-danger me-2 mt-1"></i>
                <div class="flex-grow-1">
                    <strong>Error!</strong><br>
                    <?= session()->getFlashdata('error') ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <!-- Header Card -->
            <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                <div class="gradient-header text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-gradient-success bg-opacity-20 p-2 rounded me-3">
                                <i class="fas fa-file-excel fa-lg "></i>
                            </div>
                            <h4 class="mb-0 fw-bold text-white">FORM IMPORT STOK AKTUAL</h4>
                        </div>
                        <a href="<?= base_url('template/stock-template.xlsx') ?>"
                            class="btn btn-light btn-sm d-flex align-items-center text-decoration-none"
                            style="background-color: rgba(255,255,255,0.2); border: none; color: white;"
                            onmouseover="this.style.backgroundColor='rgba(255,255,255,0.3)'"
                            onmouseout="this.style.backgroundColor='rgba(255,255,255,0.2)'">
                            <i class="fas fa-download me-2"></i>
                            Download Template
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Import Form -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <form action="<?= base_url($role . '/importStock/upload') ?>" method="post" enctype="multipart/form-data" id="importForm">
                        <!-- File Upload Area -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-3">
                                Select Excel File
                                <span class="text-danger">*</span>
                            </label>

                            <div class="drag-drop-area" id="dragDropArea">
                                <input type="file"
                                    class="file-input-hidden"
                                    id="fileExcel"
                                    name="fileExcel"
                                    accept=".xlsx,.xls,.csv"
                                    required>

                                <div id="uploadContent">
                                    <div class="file-icon">
                                        <i class="fas fa-cloud-upload-alt fa-3x"></i>
                                    </div>
                                    <p class="mb-2 fw-medium text-dark">
                                        Drop your Excel file here, or <span class="text-primary text-decoration-underline">click to browse</span>
                                    </p>
                                    <p class="small text-muted mb-0">
                                        Supports .xlsx, .xls, and .csv files up to 10MB
                                    </p>
                                </div>

                                <div id="fileSelected" class="d-none">
                                    <div class="d-flex justify-content-center align-items-center mb-3">
                                        <i class="fas fa-file-excel text-success fa-2x me-2"></i>
                                        <i class="fas fa-check-circle text-success fa-lg"></i>
                                    </div>
                                    <p class="mb-1 fw-medium text-dark" id="fileName"></p>
                                    <p class="small text-muted mb-2" id="fileSize"></p>
                                    <button type="button" class="btn btn-link btn-sm text-danger p-0" id="removeFile">
                                        Remove file
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- File Requirements -->
                        <div class="requirements-card p-3 mb-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-triangle text-warning me-2 mt-1"></i>
                                <div>
                                    <p class="fw-medium text-warning mb-2">File Requirements:</p>
                                    <ul class="small text-dark mb-0 ps-3">
                                        <li>File must be in Excel format (.xlsx, .xls) or CSV format (.csv)</li>
                                        <li>Maximum file size: 10MB</li>
                                        <li>First row should contain column headers</li>
                                        <li>Required columns: *</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between align-items-center pt-3">
                            <small class="text-muted">
                                <span class="text-danger">*</span> Required fields
                            </small>
                            <button type="submit" class="btn btn-gradient text-white px-4 py-2 fw-semibold" id="submitBtn">
                                <span id="submitText">
                                    <i class="fas fa-upload me-2"></i>
                                    Import Stock Data
                                </span>
                                <span id="loadingText" class="d-none">
                                    <div class="spinner me-2"></div>
                                    Importing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="fw-semibold text-dark mb-4">Need Help?</h5>
                    <div class="row">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <h6 class="fw-medium text-dark mb-3">Sample Data Format</h6>
                            <div class="sample-data p-3">
                                <!-- <div class="row fw-bold text-muted border-bottom pb-2 mb-2">
                                    <div class="col-4">SKU</div>
                                    <div class="col-4">Product Name</div>
                                    <div class="col-4">Stock Quantity</div>
                                </div>
                                <div class="row text-muted mb-1">
                                    <div class="col-4">SKU001</div>
                                    <div class="col-4">Widget A</div>
                                    <div class="col-4">100</div>
                                </div>
                                <div class="row text-muted">
                                    <div class="col-4">SKU002</div>
                                    <div class="col-4">Widget B</div>
                                    <div class="col-4">250</div>
                                </div> -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-medium text-dark mb-3">Import Process</h6>
                            <div class="small text-muted">
                                <div class="process-step">
                                    <div class="process-dot"></div>
                                    <span>File validation and format checking</span>
                                </div>
                                <div class="process-step">
                                    <div class="process-dot"></div>
                                    <span>Data processing and duplicate detection</span>
                                </div>
                                <div class="process-step">
                                    <div class="process-dot"></div>
                                    <span>Stock quantities update in database</span>
                                </div>
                                <div class="process-step">
                                    <div class="process-dot"></div>
                                    <span>Import summary and error report</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dragDropArea = document.getElementById('dragDropArea');
        const fileInput = document.getElementById('fileExcel');
        const uploadContent = document.getElementById('uploadContent');
        const fileSelected = document.getElementById('fileSelected');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const removeFileBtn = document.getElementById('removeFile');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const loadingText = document.getElementById('loadingText');
        const importForm = document.getElementById('importForm');

        // Drag & Drop
        dragDropArea.addEventListener('dragover', e => {
            e.preventDefault();
            dragDropArea.classList.add('dragover');
        });

        dragDropArea.addEventListener('dragleave', e => {
            e.preventDefault();
            dragDropArea.classList.remove('dragover');
        });

        dragDropArea.addEventListener('drop', e => {
            e.preventDefault();
            dragDropArea.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) handleFileSelect(files[0]);
        });

        fileInput.addEventListener('change', e => {
            if (e.target.files.length > 0) handleFileSelect(e.target.files[0]);
        });

        removeFileBtn.addEventListener('click', () => {
            fileInput.value = '';
            uploadContent.classList.remove('d-none');
            fileSelected.classList.add('d-none');
            dragDropArea.classList.remove('file-selected');
            submitBtn.disabled = false;
        });

        function handleFileSelect(file) {
            const allowedTypes = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
                'text/csv'
            ];

            if (!allowedTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls|csv)$/i)) {
                Swal.fire('Invalid File', 'Pilih file .xlsx, .xls, atau .csv', 'warning');
                return;
            }

            if (file.size > 10 * 1024 * 1024) {
                Swal.fire('File Terlalu Besar', 'Maksimal 10MB.', 'warning');
                return;
            }

            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';

            uploadContent.classList.add('d-none');
            fileSelected.classList.remove('d-none');
            dragDropArea.classList.add('file-selected');
        }

        // Form AJAX Submit
        $('#importForm').on('submit', function(e) {
            e.preventDefault();

            if (!fileInput.files.length) {
                Swal.fire('Tidak ada file', 'Pilih file terlebih dahulu', 'warning');
                return;
            }

            const formData = new FormData(this);

            submitBtn.disabled = true;
            submitText.classList.add('d-none');
            loadingText.classList.remove('d-none');

            $.ajax({
                url: '<?= site_url($role . "/importStock/upload") ?>',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: () => {
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(res) {
                    Swal.close();
                    submitBtn.disabled = false;
                    submitText.classList.remove('d-none');
                    loadingText.classList.add('d-none');

                    if (res.status === 'success') {
                        let log = '';
                        if (res.errors.length > 0) {
                            log = '<ul style="text-align:left">';
                            res.errors.forEach(e => log += `<li>${e}</li>`);
                            log += '</ul>';
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Import Berhasil',
                            html: `✅ ${res.inserted} baris berhasil diimport.<br>${log}`,
                            confirmButtonText: 'OK'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message
                        });
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Error', 'Terjadi kesalahan saat menghubungi server.', 'error');
                    submitBtn.disabled = false;
                    submitText.classList.remove('d-none');
                    loadingText.classList.add('d-none');
                }
            });
        });

        // Auto-close flash message
        setTimeout(() => {
            document.querySelectorAll('.notification').forEach(el => {
                const alert = new bootstrap.Alert(el);
                alert.close();
            });
        }, 5000);
    });
</script>

<?php $this->endSection(); ?>