<?php
require 'includes/functions.php';
$page_title = 'Discover Amazing Events'; $active = 'home';
$featured = get_events("AND e.is_featured=1 ORDER BY e.event_date LIMIT 6");
$upcoming = get_events("AND e.event_date >= CURDATE() ORDER BY e.event_date LIMIT 6");
$cats = get_categories_with_counts();
include 'includes/header.php';
?>
<?php
$totalEvents = array_sum(array_column($cats, 'total'));
$tiles = array_slice($featured ?: $upcoming, 0, 3);
?>
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <span class="eyebrow"><span class="live-dot"></span> <?= (int)$totalEvents ?> events live right now</span>
      <h1>Find the night<br>worth <em>leaving home</em> for.</h1>
      <p>Concerts, meetups, matches and markets near you &mdash; browse on the map, save your favourites and RSVP in one tap.</p>
      <form class="hero-search" action="events.php">
        <i class="bi bi-search"></i>
        <input type="text" name="q" placeholder="Search events, venues, or categories..." aria-label="Search events">
        <button class="btn btn-primary">Search</button>
      </form>
      <div class="hero-quick">
        <span>Popular:</span>
        <?php foreach (array_slice($cats, 0, 4) as $c): ?>
          <a href="events.php?cat[]=<?= $c['id'] ?>"><?= cat_icon($c['name']) ?> <?= e($c['name']) ?></a>
        <?php endforeach; ?>
      </div>
      <div class="hero-stats">
        <div><b><?= (int)$totalEvents ?></b><small>Events</small></div>
        <div><b><?= count($cats) ?></b><small>Categories</small></div>
        <div><b>Free</b><small>to join</small></div>
      </div>
    </div>
    <div class="hero-collage" aria-hidden="false">
      <?php foreach ($tiles as $n => $ev): ?>
        <a href="event.php?id=<?= $ev['id'] ?>" class="collage-tile t<?= $n + 1 ?>">
          <?= img_tag($ev['image'], $ev['title'], 'width="600" height="700"', event_fallback($ev['id'])) ?>
          <span class="collage-cap"><small><?= e($ev['cat']) ?></small><?= e($ev['title']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-header"><span class="kicker">Handpicked</span><h2>Featured events</h2><p>Hover a card and watch it light up on the map.</p></div>
    <div class="featured-layout">
      <div class="sticky-map"><div id="map"></div></div>
      <div id="featBox">
        <div class="event-grid rows">
          <?php foreach ($featured as $ev): ?><?php include 'includes/event_card.php'; ?><?php endforeach; ?>
        </div>
        <?php if (!$featured) echo '<p class="text-center text-muted">No featured events yet.</p>'; ?>
      </div>
    </div>
    <div class="mt-4"><a href="events.php" class="btn btn-secondary btn-lg">View all events <i class="bi bi-arrow-right"></i></a></div>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <div class="section-header"><span class="kicker">Browse by vibe</span><h2>Explore categories</h2><p>Pick what you are into.</p></div>
    <div class="category-grid">
      <?php foreach ($cats as $c): ?>
        <a href="events.php?cat[]=<?= $c['id'] ?>" class="category-card card">
          <div class="category-card-icon"><?= cat_icon($c['name']) ?></div>
          <div class="category-card-text">
            <h3><?= e($c['name']) ?></h3>
            <span class="category-card-count"><?= $c['total'] ?> event<?= $c['total'] != 1 ? 's' : '' ?></span>
          </div>
          <i class="bi bi-arrow-up-right category-card-go"></i>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-header"><span class="kicker">Coming up</span><h2>Upcoming events</h2><p>Mark your calendar.</p></div>
    <div class="event-grid">
      <?php foreach ($upcoming as $ev): ?><?php include 'includes/event_card.php'; ?><?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-wrap">
  <div class="container">
    <div class="cta-section">
      <div>
        <h2>Ready to find your next plan?</h2>
        <p>Join the community, RSVP in one tap and never miss what is happening around you.</p>
      </div>
      <a href="<?= is_logged() ? 'events.php' : 'register.php' ?>" class="btn btn-lg"><?= is_logged() ? 'Browse events' : 'Get started free' ?> <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</section>
<script>
EventMap.init('map');
EventMap.set(<?= json_encode(events_for_map($featured), JSON_HEX_TAG | JSON_HEX_AMP) ?>);
EventMap.bindCards(document.getElementById('featBox'));
</script>
<?php include 'includes/footer.php'; ?>
