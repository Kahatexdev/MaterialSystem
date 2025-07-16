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
<?php $pager->setSurroundCount(2); ?>

<nav aria-label="Page navigation">
    <ul class="pagination mb-0">
        <!-- Tombol Previous -->
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPrevious() ?>" aria-label="Previous">Previous</a>
            </li>
        <?php else : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getCurrent() ?>" aria-label="Previous">Previous</a>
            </li>
        <?php endif; ?>

        <!-- Link Nomor Halaman -->
        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                    <?php if ($link['active']) : ?>
                        <span class="visually-hidden">(current)</span>
                    <?php endif ?>
                </a>
            </li>
        <?php endforeach ?>

        <!-- Tombol Next -->
        <?php if ($pager->hasNext()) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getNext() ?>" aria-label="Next">Next</a>
            </li>
        <?php else : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getCurrent() ?>" aria-label="Next">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>