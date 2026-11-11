<?php // Reusable event card (new "poster" design): $ev variable chahiye
$cc = e($ev['color']); $ts = strtotime($ev['event_date']); ?>
<article class="event-card card" data-id="<?= $ev['id'] ?>">
  <a class="event-card-media" href="<?= BASE_URL ?>event.php?id=<?= $ev['id'] ?>" tabindex="-1" aria-hidden="true">
    <?= img_tag($ev['image'], $ev['title'] . ' - ' . $ev['cat'] . ' event', 'class="event-card-img" width="800" height="500"', event_fallback($ev['id'])) ?>
    <span class="event-date-badge"><b><?= date('d', $ts) ?></b><small><?= date('M', $ts) ?></small></span>
    <span class="event-price-pill"><?= price_text($ev['price']) ?></span>
  </a>
  <div class="event-card-body card-body">
    <div class="event-card-tags">
      <span class="event-card-category" style="background:<?= $cc ?>1f;color:<?= $cc ?>"><?= cat_icon($ev['cat']) ?> <?= e($ev['cat']) ?></span>
      <?php if ($ev['event_date'] < date('Y-m-d')): ?><span class="badge-past">Past</span><?php endif; ?>
    </div>
    <h3 class="event-card-title"><a href="<?= BASE_URL ?>event.php?id=<?= $ev['id'] ?>"><?= e($ev['title']) ?></a></h3>
    <p class="event-card-desc"><?= e($ev['short_desc']) ?></p>
    <div class="event-card-meta">
      <span><i class="bi bi-clock"></i> <?= date_text($ev['event_date'], $ev['event_time']) ?></span>
      <span><i class="bi bi-geo-alt"></i> <?= e($ev['venue']) ?>, <?= e($ev['city']) ?></span>
    </div>
    <div class="event-card-footer">
      <a href="<?= BASE_URL ?>event.php?id=<?= $ev['id'] ?>" class="event-card-link">View details <i class="bi bi-arrow-up-right"></i></a>
    </div>
  </div>
</article>
