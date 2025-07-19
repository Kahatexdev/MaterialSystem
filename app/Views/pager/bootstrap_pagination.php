<style>
    .pagination .page-link {
        color: #0d6efd;
    }

    /* Hover */
    .pagination .page-link:hover {
        color: #0a58ca;
    }

    /* Aktif (halaman sekarang) */
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    /* Fokus (saat ditekan tab atau klik) */
    .pagination .page-link:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>

<?php if ($pager->getPageCount() > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-end">
            <!-- First page link -->
            <?php if ($pager->hasPrevious()): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="First">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Numeric links -->
            <?php foreach ($pager->links('warehouse', 'default_full') as $link): ?>
                <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
                </li>
            <?php endforeach; ?>

            <!-- Last page link -->
            <?php if ($pager->hasNext()): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getLast() ?>" aria-label="Last">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>