<?php
require 'includes/functions.php';
require_login();
$uid = $_SESSION['user']['id'];

// Profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $avatar = upload_image($_FILES['avatar'] ?? null) ?: $_SESSION['user']['avatar'];
    $pdo->prepare("UPDATE users SET name=?, avatar=? WHERE id=?")->execute([trim($_POST['name']), $avatar, $uid]);
    if (!empty($_POST['password'])) {
        $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($_POST['password'], PASSWORD_DEFAULT), $uid]);
    }
    $_SESSION['user']['name'] = trim($_POST['name']); $_SESSION['user']['avatar'] = $avatar;
    log_action('profile_update'); flash('Profile updated.'); redirect('profile.php');
}
$n = $pdo->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC LIMIT 50"); $n->execute([$uid]); $notes = $n->fetchAll();
$pdo->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?")->execute([$uid]);   // dekh liye = read
$tab = $_GET['tab'] ?? '';
$me = $pdo->prepare("SELECT created_at FROM users WHERE id=?"); $me->execute([$uid]); $joined = $me->fetchColumn();
$rsvps = get_events("AND e.id IN (SELECT event_id FROM rsvps WHERE user_id=?) ORDER BY e.event_date", [$uid]);
$saved = get_events("AND e.id IN (SELECT event_id FROM favorites WHERE user_id=?) ORDER BY e.event_date", [$uid]);
$mine = $pdo->prepare("SELECT title, status, event_date FROM events WHERE created_by=? ORDER BY id DESC"); $mine->execute([$uid]);
$page_title = 'My Profile'; $active = 'profile';
include 'includes/header.php';
?>
<div class="dashboard page-content">
<div class="container">
  <div class="dashboard-header">
    <h1>Welcome back, <?= e(explode(' ', $_SESSION['user']['name'])[0]) ?>!</h1>
    <p>Manage your registrations, saved events and account.</p>
  </div>
  <div class="profile-card">
    <div class="profile-card-avatar"><img src="<?= BASE_URL . e($_SESSION['user']['avatar']) ?>" alt="<?= e($_SESSION['user']['name']) ?>"></div>
    <div class="profile-card-info">
      <h2><?= e($_SESSION['user']['name']) ?></h2>
      <p><?= e($_SESSION['user']['email']) ?></p>
      <p class="joined">Member since <?= date('d M Y', strtotime($joined)) ?></p>
    </div>
  </div>

  <ul class="nav nav-tabs mb-4">
    <li class="nav-item"><button class="nav-link <?= $tab != 'notes' ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#t1">My RSVPs (<?= count($rsvps) ?>)</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t2">Saved (<?= count($saved) ?>)</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t3">My Submissions</button></li>
    <li class="nav-item"><button class="nav-link <?= $tab == 'notes' ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#t5">Notifications (<?= count($notes) ?>)</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t4">Edit Profile</button></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade <?= $tab != 'notes' ? 'show active' : '' ?>" id="t1">
      <div class="event-grid compact"><?php foreach ($rsvps as $ev): ?><?php include 'includes/event_card.php'; ?><?php endforeach; ?></div>
      <?php if (!$rsvps) echo '<p class="text-muted">You have not registered for any event yet. <a href="events.php">Browse events</a></p>'; ?>
    </div>
    <div class="tab-pane fade" id="t2">
      <div class="event-grid compact"><?php foreach ($saved as $ev): ?><?php include 'includes/event_card.php'; ?><?php endforeach; ?></div>
      <?php if (!$saved) echo '<p class="text-muted">No saved events.</p>'; ?>
    </div>
    <div class="tab-pane fade" id="t3">
      <div class="table-responsive"><table class="table"><tr><th>Title</th><th>Date</th><th>Status</th></tr>
      <?php foreach ($mine as $m): ?>
        <tr><td><?= e($m['title']) ?></td><td><?= $m['event_date'] ?></td>
        <td><span class="badge bg-<?= ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'][$m['status']] ?>"><?= $m['status'] ?></span></td></tr>
      <?php endforeach; ?></table></div>
    </div>
    <div class="tab-pane fade <?= $tab == 'notes' ? 'show active' : '' ?>" id="t5">
      <?php foreach ($notes as $nt): ?>
        <div class="card card-body mb-2 <?= $nt['is_read'] ? '' : 'unread' ?>">
          <?php if ($nt['link']): ?><a class="text-dark" href="<?= BASE_URL . e($nt['link']) ?>"><?= e($nt['message']) ?></a>
          <?php else: echo e($nt['message']); endif; ?>
          <small class="text-muted"><?= $nt['created_at'] ?></small>
        </div>
      <?php endforeach; ?>
      <?php if (!$notes) echo '<p class="text-muted">No notifications.</p>'; ?>
    </div>
    <div class="tab-pane fade" id="t4">
      <form method="post" enctype="multipart/form-data" class="card card-body" style="max-width:450px">
        <label class="form-label" for="pname">Name</label><input id="pname" class="form-control mb-3" name="name" value="<?= e($_SESSION['user']['name']) ?>" required>
        <label class="form-label" for="ppass">New password (leave empty to keep old)</label><input id="ppass" type="password" class="form-control mb-3" name="password">
        <label class="form-label" for="pav">Profile picture</label><input id="pav" type="file" class="form-control mb-3" name="avatar" accept="image/*">
        <button class="btn btn-primary align-self-start">Save Changes</button>
      </form>
    </div>
  </div>
</div>
</div>
<?php include 'includes/footer.php'; ?>
