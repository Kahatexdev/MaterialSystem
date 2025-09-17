<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
<style>
    #threadModal .modal-dialog {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 400px;
        width: 100%;
        margin: 0;
        padding: 0;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
        background-color: #fff;
        z-index: 1050;
    }
</style>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $title; ?></p>
                                <h5 class="font-weight-bolder mb-0">
                                    Data Pemesanan Berdasarkan Area
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thread type dropdown modal -->
    <div class="modal fade" id="threadModal" tabindex="-1" aria-labelledby="threadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centeredmodal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title" id="threadModalLabel">Pilih Jenis Benang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="threadForm" action="" method="post">
                        <input type="hidden" id="selectedArea" name="area" value="">
                        <div class="mb-3">
                            <label for="threadType" class="form-label">Jenis Benang</label>
                            <select class="form-select" id="threadType" name="thread_type" required>
                                <option value="" selected disabled>Pilih Jenis Benang</option>
                                <?php foreach ($threadTypes ?? [] as $thread): ?>
                                    <option value="<?= $thread['id'] ?>"><?= $thread['name'] ?></option>
                                <?php endforeach; ?>
                                <!-- If you don't have thread types data yet, you can hardcode options: -->
                                <?php if (empty($threadTypes)): ?>
                                    <option value="BENANG">BENANG</option>
                                    <option value="NYLON">NYLON</option>
                                    <option value="KARET">KARET</option>
                                    <option value="SPANDEX">SPANDEX</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" id="bb" class="btn btn-info">Lanjutkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php foreach ($area as $ar) : ?>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
                <div class="card area-card" data-area="<?= $ar ?>">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $ar ?></p>
                                    <h5 class="font-weight-bolder mb-0"></h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="fas fa-industry text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2">
            <div class="card area-card" data-area="Rosso">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">ROSSO</p>
                                <h5 class="font-weight-bolder mb-0"></h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-industry text-lg opacity-10" aria-hidden="true"></i>
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
        // Get all area cards
        const areaCards = document.querySelectorAll('.area-card');

        // Add click event to each area card
        areaCards.forEach(card => {
            card.addEventListener('click', function() {
                const area = this.getAttribute('data-area');

                // Set the selected area in the hidden input
                document.getElementById('selectedArea').value = area;

                // Set the form action based on the selected area
                document.getElementById('threadForm').action = '<?= base_url($role . '/process_thread_selection') ?>';

                // Show the modal
                const threadModal = new bootstrap.Modal(document.getElementById('threadModal'));
                threadModal.show();
            });
        });

        // Handle form submission
        document.getElementById('threadForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const area = document.getElementById('selectedArea').value;
            const threadType = document.getElementById('threadType').value;

            // Redirect to the new page with area and thread type parameters
            window.location.href = '<?= base_url($role . '/pemesanan/') ?>' + area + '/' + threadType;
        });
    });
</script>
<?php $this->endSection(); ?>