<?php if (empty($rows)): ?>
    <div class="alert alert-info text-center my-3">Tidak ada data untuk grup ini.</div>
    <?php return; ?>
<?php endif; ?>

<div class="table-responsive mt-3">
    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th>Cluster</th>
                <th>Kapasitas</th>
                <th>Total Terpakai</th>
                <th>Sisa</th>
                <th>Simbol</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r):
                $kap    = (float)($r['kapasitas'] ?? 0);
                $terisi = (float)($r['total_qty'] ?? 0);
                $sisa   = max($kap - $terisi, 0);

                // Parse detail_data "no_model|item_type|kode_warna,..." -> array objek
                $detailList = [];
                if (!empty($r['detail_data'])) {
                    $parts = explode(',', $r['detail_data']);
                    foreach ($parts as $p) {
                        $p = trim($p);
                        if ($p === '') continue;
                        [$noModel, $itemType, $kodeWarna] = array_pad(explode('|', $p), 3, '');
                        $detailList[] = [
                            'no_model'   => $noModel,
                            'item_type'  => $itemType,
                            'kode_warna' => $kodeWarna,
                            // kolom ini belum ada di model kamu; biarkan kosong/null
                            'foll_up'    => '',
                            'delivery'   => '',
                            'qty'        => null,
                        ];
                    }
                }

                // detail_karung sementara kosong dari model (string '[]')
                $detailKarung = [];
                if (isset($r['detail_karung']) && is_string($r['detail_karung']) && $r['detail_karung'] !== '') {
                    $decoded = json_decode($r['detail_karung'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $detailKarung = $decoded;
                    }
                }

                // Encode aman untuk attribute HTML
                $jsonDetail = htmlspecialchars(json_encode($detailList, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                $jsonKarung = htmlspecialchars(json_encode($detailKarung, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');

                // Warna cell kapasitas
                $pct = ($kap > 0) ? ($terisi / $kap * 100) : 0;
                $cls = 'gray-cell';
                if ($pct === 0) $cls = 'gray-cell';
                elseif ($pct <= 70) $cls = 'blue-cell';
                elseif ($pct < 100) $cls = 'orange-cell';
                else $cls = 'red-cell';
            ?>
                <tr>
                    <td class="text-start"><?= esc($r['nama_cluster']) ?></td>
                    <td><?= number_format($kap, 2) ?> kg</td>
                    <td><span class="cell <?= $cls ?>"><?= number_format($terisi, 2) ?> kg</span></td>
                    <td><?= number_format($sisa, 2) ?> kg</td>
                    <td><?= esc($r['simbol_cluster'] ?? '-') ?></td>
                    <td>
                        <button
                            class="btn btn-info btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalDetail"
                            data-nama_cluster="<?= esc($r['nama_cluster']) ?>"
                            data-kapasitas="<?= $kap ?>"
                            data-total_qty="<?= $terisi ?>"
                            data-detail='<?= $jsonDetail ?>'
                            data-karung='<?= $jsonKarung ?>'>
                            <i class="fas fa-eye me-1"></i> Detail
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    modalDetail.addEventListener("show.bs.modal", function(event) {
        const button = event.relatedTarget;

        const kapasitas = Number(button.getAttribute("data-kapasitas")) || 0;
        const totalQty = Number(button.getAttribute("data-total_qty")) || 0;
        const namaCluster = button.getAttribute("data-nama_cluster") || "-";

        let detailData = [];
        try {
            detailData = JSON.parse(button.getAttribute("data-detail") || "[]");
        } catch (e) {
            detailData = [];
        }

        let detailKarung = [];
        try {
            detailKarung = JSON.parse(button.getAttribute("data-karung") || "[]");
        } catch (e) {
            detailKarung = [];
        }

        const sisa = Math.max(kapasitas - totalQty, 0);

        // Populate header info
        document.getElementById("modalKapasitas").textContent = kapasitas.toFixed(2);
        document.getElementById("modalTotalQty").textContent = totalQty.toFixed(2);
        document.getElementById("modalNamaCluster").textContent = namaCluster;
        document.getElementById("modalSisaKapasitas").textContent = sisa.toFixed(2);

        // Tabel detail
        const tableBody = document.getElementById("modalDetailTableBody");
        tableBody.innerHTML = "";

        if (detailData.length === 0) {
            tableBody.innerHTML = `
      <tr><td colspan="6" class="text-center text-muted">Tidak ada detail model di cluster ini.</td></tr>
    `;
        } else {
            detailData.forEach((item) => {
                const karungForThis = detailKarung.filter(k => k.no_model === item.no_model);
                const karungJSON = JSON.stringify(karungForThis)
                    .replace(/</g, "\\u003c")
                    .replace(/>/g, "\\u003e")
                    .replace(/&/g, "\\u0026"); // sanitize

                const row = `
        <tr class="fade-in">
          <td>${item.no_model || ''}</td>
          <td>${item.kode_warna || ''}</td>
          <td>${item.foll_up || ''}</td>
          <td>${item.delivery || ''}</td>
          <td>${item.qty != null ? item.qty + ' kg' : '-'}</td>
          <td>
            <button class="btn btn-info btn-sm show-karung" data-karung='${karungJSON}'>
              <i class="fas fa-eye"></i>
            </button>
          </td>
        </tr>
      `;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
        }

        // kosongkan panel karung
        document.getElementById("modalKarungList").innerHTML = "";
    });
</script>