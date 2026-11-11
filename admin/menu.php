<?php require_staff(); $cur = basename($_SERVER['PHP_SELF']);
// Admin ko sab menu, moderator ko sirf Dashboard aur Events
$items = ['index.php' => 'Dashboard', 'events.php' => 'Events'];
if (is_admin()) $items += ['categories.php' => 'Categories', 'users.php' => 'Users', 'settings.php' => 'Settings & API', 'logs.php' => 'Activity Logs'];
?>
<div class="container mt-3">
  <span class="badge bg-<?= is_admin() ? 'danger' : 'info' ?> mb-2"><?= is_admin() ? 'Administrator' : 'Moderator' ?></span>
  <ul class="nav nav-pills mb-3 flex-wrap">
    <?php foreach ($items as $f => $t): ?>
      <li class="nav-item"><a class="nav-link <?= $cur == $f ? 'active bg-main' : 'text-main' ?>" href="<?= BASE_URL ?>admin/<?= $f ?>"><?= $t ?></a></li>
    <?php endforeach; ?>
  </ul>
</div>
