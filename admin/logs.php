<?php
require '../includes/functions.php';
require_admin();
$logs = $pdo->query("SELECT l.*, u.name FROM activity_logs l LEFT JOIN users u ON u.id=l.user_id ORDER BY l.id DESC LIMIT 200")->fetchAll();
$page_title = 'Activity Logs'; $active = 'admin';
include '../includes/header.php'; include 'menu.php';
?>
<div class="container">
  <h4>System / Activity Logs <small class="text-muted">(latest 200)</small></h4>
  <div class="table-responsive"><table class="table table-sm table-striped bg-white">
    <tr><th>Time</th><th>User</th><th>Action</th><th>Details</th><th>IP</th></tr>
    <?php foreach ($logs as $l): ?>
      <tr><td><?= $l['created_at'] ?></td><td><?= e($l['name'] ?? 'guest') ?></td>
      <td><span class="badge bg-main"><?= e($l['action']) ?></span></td><td><?= e($l['details']) ?></td><td><?= e($l['ip']) ?></td></tr>
    <?php endforeach; ?>
  </table></div>
</div>
<?php include '../includes/footer.php'; ?>
