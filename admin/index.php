<?php
require '../includes/functions.php';
require_staff();
$page_title = 'Admin Dashboard'; $active = 'admin';
$count = function ($sql) use ($pdo) { return $pdo->query($sql)->fetchColumn(); };
$stats = [
  ['Users', $count("SELECT COUNT(*) FROM users"), 'bi-people', 'primary'],
  ['Approved Events', $count("SELECT COUNT(*) FROM events WHERE status='approved'"), 'bi-calendar-check', 'success'],
  ['Pending Events', $count("SELECT COUNT(*) FROM events WHERE status='pending'"), 'bi-hourglass-split', 'warning'],
  ['Total RSVPs', $count("SELECT COUNT(*) FROM rsvps"), 'bi-ticket-perforated', 'danger'],
  ['Reviews', $count("SELECT COUNT(*) FROM reviews"), 'bi-star', 'info'],
  ['Saved (favorites)', $count("SELECT COUNT(*) FROM favorites"), 'bi-heart', 'secondary'],
];
$top = $pdo->query("SELECT e.title, COUNT(r.id) AS total FROM events e LEFT JOIN rsvps r ON r.event_id=e.id GROUP BY e.id, e.title ORDER BY total DESC LIMIT 5")->fetchAll();
$bycat = $pdo->query("SELECT c.name, c.color, COUNT(e.id) AS total FROM categories c LEFT JOIN events e ON e.category_id=c.id AND e.status='approved' GROUP BY c.id, c.name, c.color")->fetchAll();
$logs = $pdo->query("SELECT l.*, u.name FROM activity_logs l LEFT JOIN users u ON u.id=l.user_id ORDER BY l.id DESC LIMIT 6")->fetchAll();
include '../includes/header.php'; include 'menu.php';
?>
<script src="<?= BASE_URL ?>assets/vendor/chartjs/chart.umd.js"></script>
<div class="container">
  <div class="row g-3 mb-4">
    <?php foreach ($stats as [$t, $n, $i, $c]): ?>
      <div class="col-6 col-lg-2"><div class="card text-bg-<?= $c ?> shadow-sm"><div class="card-body">
        <i class="bi <?= $i ?> fs-3"></i><h2><?= $n ?></h2><small><?= $t ?></small></div></div></div>
    <?php endforeach; ?>
  </div>
  <div class="row g-3">
    <div class="col-lg-6"><div class="card shadow-sm"><div class="card-header">Top events by RSVPs</div><div class="card-body"><div class="chart-box"><canvas id="c1"></canvas></div></div></div></div>
    <div class="col-lg-6"><div class="card shadow-sm"><div class="card-header">Approved events by category</div><div class="card-body"><div class="chart-box"><canvas id="c2"></canvas></div></div></div></div>
  </div>
  <?php if (is_admin()): ?>
  <div class="card shadow-sm mt-3"><div class="card-header d-flex justify-content-between">Recent activity <a href="logs.php">View all logs</a></div>
    <table class="table table-sm mb-0"><?php foreach ($logs as $l): ?>
      <tr><td><?= $l['created_at'] ?></td><td><?= e($l['name'] ?? 'guest') ?></td><td><span class="badge bg-main"><?= e($l['action']) ?></span></td><td><?= e($l['details']) ?></td></tr>
    <?php endforeach; ?></table></div>
  <?php endif; ?>
</div>
<script>
Chart.defaults.color = '#A9ADB8'; Chart.defaults.borderColor = '#272B34';
new Chart(document.getElementById('c1'), {type: 'bar', data: {labels: <?= json_encode(array_column($top, 'title')) ?>,
  datasets: [{label: 'RSVPs', data: <?= json_encode(array_map('intval', array_column($top, 'total'))) ?>, backgroundColor: '#C6F432', borderRadius: 6}]},
  options: {maintainAspectRatio: false, plugins: {legend: {display: false}}, scales: {y: {beginAtZero: true, ticks: {precision: 0}}}}});
new Chart(document.getElementById('c2'), {type: 'doughnut', data: {labels: <?= json_encode(array_column($bycat, 'name')) ?>,
  datasets: [{data: <?= json_encode(array_map('intval', array_column($bycat, 'total'))) ?>, backgroundColor: <?= json_encode(array_column($bycat, 'color')) ?>}]}, options: {maintainAspectRatio: false}});
</script>
<?php include '../includes/footer.php'; ?>
