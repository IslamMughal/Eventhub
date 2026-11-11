<?php
require 'includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
$rows = get_events("AND e.id=?", [$id]);
if (!$rows) {
    http_response_code(404); $page_title = 'Event Not Found'; $active = 'events'; include 'includes/header.php';
    echo '<div class="container" style="padding:5rem 0;text-align:center"><h1 style="font-size:1.75rem;margin-bottom:1rem">Event Not Found</h1><p style="color:var(--text-secondary);margin-bottom:2rem">The event you are looking for does not exist or has been removed.</p><a href="events.php" class="btn btn-primary">Browse Events</a></div>';
    include 'includes/footer.php'; exit;
}
$ev = $rows[0];

$gallery = $pdo->prepare("SELECT image FROM event_images WHERE event_id=?"); $gallery->execute([$id]);
$images = array_merge([$ev['image']], array_column($gallery->fetchAll(), 'image'));

$rv = $pdo->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON u.id=r.user_id WHERE r.event_id=? ORDER BY r.id DESC");
$rv->execute([$id]); $reviews = $rv->fetchAll();
$avg = $reviews ? round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1) : 0;

$going = $saved = false;
if (is_logged()) {
    $uid = $_SESSION['user']['id'];
    $s = $pdo->prepare("SELECT 1 FROM rsvps WHERE user_id=? AND event_id=?"); $s->execute([$uid, $id]); $going = (bool)$s->fetch();
    $s = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id=? AND event_id=?"); $s->execute([$uid, $id]); $saved = (bool)$s->fetch();
}
$isPast = $ev['event_date'] <= date('Y-m-d');
$reviewed = false;
if (is_logged()) { $s = $pdo->prepare("SELECT 1 FROM reviews WHERE user_id=? AND event_id=?"); $s->execute([$uid, $id]); $reviewed = (bool)$s->fetch(); }
$url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . BASE_URL . 'event.php?id=' . $id;
$page_title = $ev['title']; $active = 'events';
$page_desc = $ev['short_desc'];
include 'includes/header.php';
$cc = e($ev['color']);
?>
<!-- SEO structured data -->
<script type="application/ld+json"><?= json_encode(['@context' => 'https://schema.org', '@type' => 'Event', 'name' => $ev['title'],
  'startDate' => $ev['event_date'], 'description' => $ev['short_desc'],
  'location' => ['@type' => 'Place', 'name' => $ev['venue'], 'address' => $ev['address'] . ', ' . $ev['city']]], JSON_HEX_TAG) ?></script>

<div class="event-detail page-content">
  <div class="container crumb-bar">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="events.php">Events</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?= e($ev['title']) ?></li>
    </ol></nav>
  </div>

  <!-- Image gallery (carousel) -->
  <div id="gal" class="carousel slide detail-carousel" data-bs-ride="carousel">
    <div class="carousel-inner">
      <?php foreach ($images as $i => $img): ?>
        <div class="carousel-item <?= $i == 0 ? 'active' : '' ?>">
          <?= img_tag($img, $ev['title'] . ' - photo ' . ($i + 1), 'class="d-block w-100" width="1200" height="400"', event_fallback($ev['id'])) ?>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (count($images) > 1): ?>
      <button class="carousel-control-prev" type="button" data-bs-target="#gal" data-bs-slide="prev" aria-label="Previous"><span class="carousel-control-prev-icon"></span></button>
      <button class="carousel-control-next" type="button" data-bs-target="#gal" data-bs-slide="next" aria-label="Next"><span class="carousel-control-next-icon"></span></button>
    <?php endif; ?>
  </div>

  <div class="container">
    <div class="event-detail-content">
      <div class="event-detail-main">
        <span class="event-card-category" style="margin-bottom:1rem;display:inline-flex;background:<?= $cc ?>1f;color:<?= $cc ?>"><?= cat_icon($ev['cat']) ?> <?= e($ev['cat']) ?></span>
        <h1><?= e($ev['title']) ?></h1>
        <?php if ($avg): ?><div class="mb-2 text-warning">★ <?= $avg ?> <span class="text-muted">(<?= count($reviews) ?> review<?= count($reviews) != 1 ? 's' : '' ?>)</span></div><?php endif; ?>
        <div class="event-detail-meta">
          <div class="event-detail-meta-item"><?= svg('cal') ?> <?= date('d M Y', strtotime($ev['event_date'])) ?></div>
          <?php if ($ev['event_time']): ?><div class="event-detail-meta-item"><?= svg('clock') ?> <?= date('h:i A', strtotime($ev['event_time'])) ?></div><?php endif; ?>
          <div class="event-detail-meta-item"><?= svg('pin') ?> <?= e($ev['venue']) ?>, <?= e($ev['city']) ?></div>
        </div>
        <div class="event-detail-desc">
          <h3 style="font-size:1.125rem;font-weight:700;margin-bottom:0.75rem;color:var(--text-primary)">About This Event</h3>
          <p><?= nl2br(e($ev['description'])) ?></p>
        </div>
        <div class="event-detail-organizer">
          <h3>Organized by</h3>
          <p><?= e($ev['organizer_name']) ?></p>
          <small><?= svg('mail') ?> <?= e($ev['organizer_contact']) ?></small>
        </div>

        <!-- Reviews -->
        <h3 class="mt-5 mb-3" id="reviews" style="font-size:1.25rem;font-weight:700">Reviews &amp; Ratings
          <?php if ($avg): ?><span class="text-warning ms-2">★ <?= $avg ?></span><small class="text-muted"> (<?= count($reviews) ?>)</small><?php endif; ?></h3>

        <?php
        // Review form kis ko dikhega:
        $needPast   = setting('review_after_event', '0') == '1';          // admin setting: sirf event ke baad
        $canReview  = is_logged() && $going && !$reviewed && (!$needPast || $isPast);
        ?>
        <?php if (setting('reviews_enabled') != '1'): ?>
          <div class="alert alert-secondary">Reviews are currently disabled by the admin.</div>
        <?php elseif (!is_logged()): ?>
          <div class="alert alert-info"><i class="bi bi-star"></i> <a href="login.php"><strong>Login</strong></a> or <a href="register.php"><strong>register</strong></a> to rate this event and share your feedback.</div>
        <?php elseif ($reviewed): ?>
          <div class="alert alert-success"><i class="bi bi-check-circle"></i> Thank you! You have already reviewed this event.</div>
        <?php else: ?>
          <?php if (!$going): ?>
            <div class="alert alert-warning"><i class="bi bi-info-circle"></i> Register for this event (RSVP button on the right) to unlock rating and feedback.</div>
          <?php elseif ($needPast && !$isPast): ?>
            <div class="alert alert-info"><i class="bi bi-clock"></i> You can rate this event after it takes place.</div>
          <?php endif; ?>
          <form method="post" action="actions.php" class="card card-body mb-3 review-form">
            <fieldset <?= $canReview ? '' : 'disabled' ?>>
              <input type="hidden" name="do" value="review"><input type="hidden" name="event_id" value="<?= $id ?>">
              <label class="form-label d-block">Your rating</label>
              <div class="star-rating mb-3">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                  <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" required><label for="star<?= $i ?>" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
              </div>
              <label for="comment" class="form-label">Your feedback</label>
              <textarea name="comment" id="comment" rows="3" class="form-control mb-3" placeholder="Tell others what you thought about this event..." required></textarea>
              <button class="btn btn-primary">Submit Review</button>
            </fieldset>
          </form>
        <?php endif; ?>

        <?php if (!$reviews) echo '<p class="text-muted">No reviews yet. Be the first to review!</p>'; ?>
        <?php foreach ($reviews as $r): ?>
          <div class="review-card">
            <strong><?= e($r['name']) ?></strong> <span class="text-warning"><?= str_repeat('★', $r['rating']) ?><span style="color:#D1D5DB"><?= str_repeat('★', 5 - $r['rating']) ?></span></span>
            <small class="text-muted ms-2"><?= date('d M Y', strtotime($r['created_at'])) ?></small><br>
            <span class="text-muted"><?= e($r['comment']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="event-detail-sidebar">
        <div class="sidebar-card card card-body">
          <div class="price"><?= price_text($ev['price']) ?> <?php if ($ev['price'] > 0): ?><span>/ person</span><?php endif; ?></div>
          <div class="sidebar-info">
            <div class="sidebar-info-item"><?= svg('cal') ?> <?= date_text($ev['event_date'], $ev['event_time']) ?></div>
            <div class="sidebar-info-item"><?= svg('pin') ?> <span><?= e($ev['venue']) ?><br><small class="text-muted"><?= e($ev['address']) ?>, <?= e($ev['city']) ?></small></span></div>
            <div class="sidebar-info-item"><?= svg('mail') ?> <?= e($ev['organizer_contact']) ?></div>
          </div>
          <?php if (is_logged()): ?>
            <form method="post" action="actions.php">
              <input type="hidden" name="event_id" value="<?= $id ?>">
              <button name="do" value="rsvp" class="btn <?= $going ? 'btn-success' : 'btn-primary' ?> btn-lg">
                <?= $going ? '✔ Registered (click to cancel)' : 'RSVP / Register' ?></button>
              <button name="do" value="save" class="btn btn-secondary mt-2"><i class="bi <?= $saved ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i> <?= $saved ? 'Saved' : 'Save event' ?></button>
            </form>
          <?php else: ?>
            <div class="alert alert-info mb-0"><a href="login.php"><strong>Login</strong></a> or <a href="register.php"><strong>Register</strong></a> to RSVP, save and review events.</div>
          <?php endif; ?>
          <a class="btn btn-outline mt-3" target="_blank" rel="noopener"
             href="https://www.google.com/maps/dir/?api=1&destination=<?= $ev['lat'] ?>,<?= $ev['lng'] ?>"><i class="bi bi-sign-turn-right"></i> Get Directions</a>
        </div>

        <div class="sidebar-card card card-body mt-3">
          <strong class="mb-2"><i class="bi bi-share"></i> Share this event</strong>
          <div class="share-row">
            <a class="btn btn-secondary btn-sm" target="_blank" rel="noopener" href="https://wa.me/?text=<?= urlencode($ev['title'] . ' ' . $url) ?>"><i class="bi bi-whatsapp"></i> WhatsApp</a>
            <a class="btn btn-secondary btn-sm" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($url) ?>"><i class="bi bi-facebook"></i> Facebook</a>
            <a class="btn btn-secondary btn-sm" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?text=<?= urlencode($ev['title']) ?>&url=<?= urlencode($url) ?>"><i class="bi bi-twitter-x"></i> X</a>
            <button class="btn btn-secondary btn-sm" type="button" onclick="navigator.clipboard.writeText('<?= e($url) ?>');this.innerText='Copied!'"><i class="bi bi-link-45deg"></i> Copy link</button>
          </div>
        </div>
        <div id="mini" class="mt-3" role="img" aria-label="Map showing <?= e($ev['venue']) ?>"></div>
      </div>
    </div>
  </div>
</div>
<script>
var m = L.map('mini').setView([<?= $ev['lat'] ?>, <?= $ev['lng'] ?>], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; OpenStreetMap'}).addTo(m);
L.marker([<?= $ev['lat'] ?>, <?= $ev['lng'] ?>]).addTo(m).bindPopup("<?= e($ev['venue']) ?>").openPopup();
</script>
<?php include 'includes/footer.php'; ?>
