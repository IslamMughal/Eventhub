<?php
require 'includes/functions.php';
$page_title = 'Discover Events'; $active = 'events';

$events = search_events($_GET);
$per = 6; $total = count($events); $pages = max(1, ceil($total / $per));
$page = max(1, min($pages, (int)($_GET['page'] ?? 1)));
$list = array_slice($events, ($page - 1) * $per, $per);

// AJAX request: sirf list + map data JSON me wapis (page reload nahi hota)
if (isset($_GET['ajax'])) {
    ob_start(); include 'includes/event_list.php'; $html = ob_get_clean();
    header('Content-Type: application/json');
    echo json_encode(['html' => $html, 'events' => events_for_map($events)]);
    exit;
}

$q = trim($_GET['q'] ?? ''); $cats = $_GET['cat'] ?? []; $city = trim($_GET['city'] ?? ''); $when = $_GET['when'] ?? '';
include 'includes/header.php';
?>
<section class="events-hero">
  <div class="container">
    <h1 >Discover Events</h1>
    <p >Find the perfect event for you</p>
    <form method="get" id="filterForm">
      <div class="d-flex flex-wrap align-items-center gap-3 py-3">
        <div class="search-box">
          <?= svg('search') ?>
          <input type="text" name="q" id="q" value="<?= e($q) ?>" placeholder="Search by event name, category, venue or organizer..." aria-label="Search events">
        </div>
      </div>
      <div class="filter-chips" id="filterChips">
        <button type="button" class="filter-chip <?= !$cats ? 'active' : '' ?>" id="chipAll">All Events</button>
        <?php foreach (get_categories() as $c): ?>
          <label class="filter-chip <?= in_array($c['id'], $cats) ? 'active' : '' ?>">
            <input type="checkbox" name="cat[]" value="<?= $c['id'] ?>" <?= in_array($c['id'], $cats) ? 'checked' : '' ?>>
            <?= cat_icon($c['name']) ?> <?= e($c['name']) ?>
          </label>
        <?php endforeach; ?>
      </div>
      <div class="filter-row mt-3">
        <div><label for="city">City</label><input type="text" class="form-control" id="city" name="city" value="<?= e($city) ?>" placeholder="Any city"></div>
        <div><label for="when">When</label><select class="form-select" id="when" name="when">
          <option value="">All dates</option>
          <option value="upcoming" <?= $when == 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
          <option value="past" <?= $when == 'past' ? 'selected' : '' ?>>Past</option></select></div>
        <div><label for="from">From</label><input type="date" class="form-control" id="from" name="from" value="<?= e($_GET['from'] ?? '') ?>"></div>
        <div><label for="to">To</label><input type="date" class="form-control" id="to" name="to" value="<?= e($_GET['to'] ?? '') ?>"></div>
        <div><button type="button" id="resetBtn" class="btn btn-secondary" title="Clear / Reset"><i class="bi bi-x-circle"></i> Reset</button></div>
      </div>
    </form>
  </div>
</section>

<section class="section" style="padding:2rem 0 4rem">
  <div class="container-fluid px-lg-5">
    <!-- Mobile: List / Map toggle -->
    <div class="filter-chips mb-3 d-lg-none">
      <button type="button" id="showList" class="filter-chip active">List</button>
      <button type="button" id="showMap" class="filter-chip">Map</button>
    </div>
    <div class="row g-4">
      <div class="col-lg-7" id="listCol"><div id="listBox"><?php include 'includes/event_list.php'; ?></div></div>
      <div class="col-lg-5 d-none d-lg-block" id="mapCol"><div class="sticky-map"><div id="map"></div></div></div>
    </div>
  </div>
</section>

<script>
EventMap.init('map');
EventMap.set(<?= json_encode(events_for_map($events), JSON_HEX_TAG | JSON_HEX_AMP) ?>);
var listBox = document.getElementById('listBox'), form = document.getElementById('filterForm');
var chipAll = document.getElementById('chipAll');
EventMap.bindCards(listBox);

// ---- Bina page reload ke filter/search (AJAX) ----
function load(qs) {
  fetch('events.php?' + qs + '&ajax=1').then(function (r) { return r.json(); }).then(function (d) {
    listBox.innerHTML = d.html;
    EventMap.set(d.events); EventMap.bindCards(listBox);
    history.replaceState(null, '', 'events.php?' + qs);
  });
}
function applyFilters() { load(new URLSearchParams(new FormData(form)).toString()); }
function syncChips() {
  var any = false;
  form.querySelectorAll('.filter-chip input').forEach(function (i) { i.parentElement.classList.toggle('active', i.checked); if (i.checked) any = true; });
  chipAll.classList.toggle('active', !any);
}

form.addEventListener('submit', function (e) { e.preventDefault(); applyFilters(); });
form.querySelectorAll('.filter-chip input').forEach(function (el) { el.addEventListener('change', function () { syncChips(); applyFilters(); }); });
form.querySelectorAll('select, input[type=date]').forEach(function (el) { el.addEventListener('change', applyFilters); });
var timer; // typing ke 0.4 sec baad real-time search
form.querySelectorAll('input[type=text]').forEach(function (el) {
  el.addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(applyFilters, 400); });
});
function resetAll() {
  form.querySelectorAll('input[type=text], input[type=date]').forEach(function (i) { i.value = ''; });
  form.querySelectorAll('.filter-chip input').forEach(function (i) { i.checked = false; });
  form.querySelector('select').value = ''; syncChips(); applyFilters();
}
document.getElementById('resetBtn').onclick = resetAll;
chipAll.onclick = resetAll;
listBox.addEventListener('click', function (e) {          // pagination bhi AJAX se
  var a = e.target.closest('.page-link'); if (!a) return;
  e.preventDefault(); load(new URL(a.href).search.slice(1));
});

// ---- Mobile toggle ----
var listCol = document.getElementById('listCol'), mapCol = document.getElementById('mapCol');
var bList = document.getElementById('showList'), bMap = document.getElementById('showMap');
bMap.onclick = function () {
  listCol.classList.add('d-none', 'd-lg-block'); mapCol.classList.remove('d-none');
  bMap.classList.add('active'); bList.classList.remove('active'); EventMap.map.invalidateSize();
};
bList.onclick = function () {
  listCol.classList.remove('d-none'); mapCol.classList.add('d-none');
  bList.classList.add('active'); bMap.classList.remove('active');
};
</script>
<?php include 'includes/footer.php'; ?>
