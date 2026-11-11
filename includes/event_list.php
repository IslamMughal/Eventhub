<?php // List + pagination. Chahiye: $events, $list, $total, $pages, $page  (AJAX aur normal dono use karte hain)
$keep = array_diff_key($_GET, ['ajax' => 1]); ?>
<?php if ($events): ?>
  <p class="events-count">Showing <?= $total ?> event<?= $total != 1 ? 's' : '' ?></p>
<?php else: ?>
  <div class="no-results">
    <h3>No results found</h3>
    <p>Try changing your search or filters, or press Reset.</p>
  </div>
<?php endif; ?>
<div class="event-grid rows">
  <?php foreach ($list as $ev): ?><?php include __DIR__ . '/event_card.php'; ?><?php endforeach; ?>
</div>
<?php if ($pages > 1): ?>
  <nav class="mt-4"><ul class="pagination flex-wrap">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <li class="page-item <?= $i == $page ? 'active' : '' ?>"><a class="page-link" href="?<?= http_build_query(array_merge($keep, ['page' => $i])) ?>"><?= $i ?></a></li>
    <?php endfor; ?>
  </ul></nav>
<?php endif; ?>
