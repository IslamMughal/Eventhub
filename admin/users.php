<?php
require '../includes/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id']; $do = $_POST['do'];
    if ($id == $_SESSION['user']['id']) { flash('You cannot change your own account here.', 'danger'); redirect('users.php'); }
    if ($do == 'toggle') $pdo->prepare("UPDATE users SET status = IF(status='active','inactive','active') WHERE id=?")->execute([$id]);
    if ($do == 'role' && in_array($_POST['role'], ['user', 'moderator', 'admin'])) $pdo->prepare("UPDATE users SET role=? WHERE id=?")->execute([$_POST['role'], $id]);
    if ($do == 'delete') $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    log_action('user_' . $do, "User #$id " . ($_POST['role'] ?? ''));
    flash('Done.'); redirect('users.php');
}
$users = $pdo->query("SELECT * FROM users ORDER BY id")->fetchAll(); $page_title = 'Users'; $active = 'admin';
include '../includes/header.php'; include 'menu.php';
?>
<div class="container">
  <h4>Registered Users</h4>
  <p class="text-muted small">Roles: <b>user</b> (RSVP, save, review, submit) - <b>moderator</b> (approve/reject/feature events) - <b>admin</b> (everything).</p>
  <div class="table-responsive"><table class="table bg-white align-middle">
    <tr><th></th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr>
    <?php foreach ($users as $u): ?>
    <tr>
      <td><img src="<?= BASE_URL . e($u['avatar']) ?>" width="36" height="36" class="rounded-circle" style="object-fit:cover" alt="avatar of <?= e($u['name']) ?>"></td>
      <td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td>
      <td>
        <form method="post" class="d-flex gap-1"><input type="hidden" name="id" value="<?= $u['id'] ?>"><input type="hidden" name="do" value="role">
          <select name="role" class="form-select form-select-sm"><?php foreach (['user', 'moderator', 'admin'] as $r): ?><option <?= $u['role'] == $r ? 'selected' : '' ?>><?= $r ?></option><?php endforeach; ?></select>
          <button class="btn btn-sm btn-primary">Set</button></form></td>
      <td><span class="badge bg-<?= $u['status'] == 'active' ? 'success' : 'dark' ?>"><?= $u['status'] ?></span></td>
      <td><form method="post" class="d-flex gap-1" onsubmit="return confirm('Are you sure?')">
        <input type="hidden" name="id" value="<?= $u['id'] ?>">
        <button name="do" value="toggle" class="btn btn-sm btn-warning">Activate / Deactivate</button>
        <button name="do" value="delete" class="btn btn-sm btn-danger">Delete</button>
      </form></td>
    </tr>
    <?php endforeach; ?>
  </table></div>
</div>
<?php include '../includes/footer.php'; ?>
