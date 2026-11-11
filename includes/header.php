<?php
// Page se pehle $page_title, $page_desc (SEO) aur $active ('home','events','categories','profile') set karo
$page_title = $page_title ?? SITE_NAME;
$page_desc  = $page_desc ?? 'Discover local and online events near you.';
$active     = $active ?? '';
$unread = 0;
if (is_logged()) { $u = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0"); $u->execute([$_SESSION['user']['id']]); $unread = $u->fetchColumn(); }
function nav_class($name) { global $active; return 'nav-link' . ($active == $name ? ' active' : ''); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title) ?> | <?= SITE_NAME ?></title>
  <meta name="description" content="<?= e($page_desc) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="<?= BASE_URL ?>assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>assets/vendor/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>assets/vendor/leaflet/leaflet.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
  <script src="<?= BASE_URL ?>assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
  <script src="<?= BASE_URL ?>assets/vendor/leaflet/leaflet.js"></script>
  <script src="<?= BASE_URL ?>assets/js/map.js"></script>
</head>
<body>
<!-- ===== Mobile top bar (hidden on desktop) ===== -->
<header class="topbar">
  <a href="<?= BASE_URL ?>index.php" class="logo"><span class="logo-mark"><i class="bi bi-lightning-charge-fill"></i></span><span><?= SITE_NAME ?></span></a>
  <button class="topbar-toggle" type="button" id="sideToggle" aria-label="Open menu" aria-controls="sidebar"><i class="bi bi-list"></i></button>
</header>
<div class="side-backdrop" id="sideBackdrop"></div>

<div class="app-shell">
<!-- ===== Left sidebar navigation ===== -->
<aside class="sidebar" id="sidebar">
  <a href="<?= BASE_URL ?>index.php" class="logo">
    <span class="logo-mark"><i class="bi bi-lightning-charge-fill"></i></span>
    <span><?= SITE_NAME ?></span>
  </a>

  <nav class="side-nav" aria-label="Main">
    <span class="side-label">Explore</span>
    <a href="<?= BASE_URL ?>index.php" class="<?= nav_class('home') ?>"><i class="bi bi-house-door"></i> Home</a>
    <a href="<?= BASE_URL ?>events.php" class="<?= nav_class('events') ?>"><i class="bi bi-compass"></i> Events</a>
    <a href="<?= BASE_URL ?>categories.php" class="<?= nav_class('categories') ?>"><i class="bi bi-grid-1x2"></i> Categories</a>
    <?php if (is_logged() && setting('allow_submissions') == '1'): ?>
      <a href="<?= BASE_URL ?>submit_event.php" class="<?= nav_class('submit') ?>"><i class="bi bi-plus-circle"></i> Submit Event</a>
    <?php endif; ?>
    <?php if (is_staff()): ?>
      <span class="side-label">Manage</span>
      <a href="<?= BASE_URL ?>admin/index.php" class="<?= nav_class('admin') ?>"><i class="bi bi-speedometer2"></i> Admin Panel</a>
    <?php endif; ?>
    <?php if (is_logged()): ?>
      <span class="side-label">Account</span>
      <a href="<?= BASE_URL ?>profile.php" class="<?= nav_class('profile') ?>"><i class="bi bi-person"></i> My Profile</a>
      <a href="<?= BASE_URL ?>profile.php?tab=notes" class="nav-link"><i class="bi bi-bell"></i> Notifications
        <?php if ($unread): ?><span class="side-badge"><?= $unread ?></span><?php endif; ?></a>
    <?php endif; ?>
  </nav>

  <div class="side-bottom">
    <?php if (is_logged()): ?>
      <div class="side-user">
        <img class="nav-avatar" src="<?= BASE_URL . e($_SESSION['user']['avatar']) ?>" alt="<?= e($_SESSION['user']['name']) ?>">
        <div class="side-user-info"><strong><?= e(explode(' ', $_SESSION['user']['name'])[0]) ?></strong><small><?= e(ucfirst($_SESSION['user']['role'])) ?></small></div>
        <a href="<?= BASE_URL ?>logout.php" class="side-logout" title="Logout" aria-label="Logout"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    <?php else: ?>
      <a href="<?= BASE_URL ?>login.php" class="btn btn-secondary w-100 mb-2"><i class="bi bi-box-arrow-in-right"></i> Login</a>
      <a href="<?= BASE_URL ?>register.php" class="btn btn-primary w-100">Sign Up</a>
    <?php endif; ?>
  </div>
</aside>

<div class="app-body">
<?php if (empty($flash_inline) && isset($_SESSION['flash'])): ?><div class="container flash-wrap"><?php show_flash(); ?></div><?php endif; ?>
