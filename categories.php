<?php
require 'includes/functions.php';
$page_title = 'Categories'; $active = 'categories';
$cats = get_categories_with_counts();
include 'includes/header.php';
?>
<section class="categories-hero">
  <div class="container"><h1>Explore Categories</h1><p>Find events that match your interests and passions</p></div>
</section>
<section class="section">
  <div class="container">
    <div class="categories-grid">
      <?php foreach ($cats as $c):
        $sample = get_events("AND e.category_id=? ORDER BY e.event_date LIMIT 3", [$c['id']]); $cc = e($c['color']); ?>
        <div class="category-detail-card card">
          <div class="category-detail-header">
            <div class="category-detail-icon" style="background:<?= $cc ?>1f;color:<?= $cc ?>"><?= cat_icon($c['name']) ?></div>
            <div><h3><?= e($c['name']) ?></h3>
              <span style="font-size:0.8125rem;color:var(--text-secondary)"><?= $c['total'] ?> event<?= $c['total'] != 1 ? 's' : '' ?> available</span></div>
          </div>
          <p><?= e(cat_desc($c['name'])) ?></p>
          <?php if ($sample): ?>
            <div class="category-detail-events">
              <?php foreach ($sample as $s): ?><span><strong><?= e($s['title']) ?></strong> &mdash; <?= date('d M Y', strtotime($s['event_date'])) ?></span><?php endforeach; ?>
            </div>
          <?php endif; ?>
          <a href="events.php?cat[]=<?= $c['id'] ?>" class="btn btn-primary" style="width:100%">View <?= e($c['name']) ?> Events</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<section class="cta-wrap">
  <div class="container">
    <div class="cta-section">
      <div><h2>Can't find what you're looking for?</h2><p>Browse all events and discover something new.</p></div>
      <a href="events.php" class="btn btn-lg">Browse all events <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
