<?php
require '../includes/functions.php';
require_staff();
// Actions: approve / reject / feature (admin + moderator), delete (sirf admin)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id']; $do = $_POST['do'];
    $s = $pdo->prepare("SELECT title, created_by FROM events WHERE id=?"); $s->execute([$id]); $ev = $s->fetch();
    if ($ev) {
        if ($do == 'approve' || $do == 'reject') {
            $pdo->prepare("UPDATE events SET status=? WHERE id=?")->execute([$do == 'approve' ? 'approved' : 'rejected', $id]);
            if ($ev['created_by']) notify($ev['created_by'], 'Your event "' . $ev['title'] . '" was ' . ($do == 'approve' ? 'approved and is now live.' : 'rejected by the admin.'), $do == 'approve' ? "event.php?id=$id" : 'profile.php');
        }
        if ($do == 'feature') $pdo->prepare("UPDATE events SET is_featured = 1 - is_featured WHERE id=?")->execute([$id]);
        if ($do == 'delete' && is_admin()) $pdo->prepare("DELETE FROM events WHERE id=?")->execute([$id]);
        log_action('event_' . $do, "#$id " . $ev['title']);
        flash('Done.');
    }
    redirect('events.php');
}
$rows = $pdo->query("SELECT e.*, c.name AS cat, u.name AS author FROM events e JOIN categories c ON c.id=e.category_id
                     LEFT JOIN users u ON u.id=e.created_by ORDER BY FIELD(e.status,'pending','approved','rejected'), e.event_date")->fetchAll();
$page_title = 'Manage Events'; $active = 'admin';
include '../includes/header.php'; include 'menu.php';
?>
<div class="container">
  <div class="d-flex justify-content-between mb-2"><h4>All Events</h4>
    <a href="event_form.php" class="btn btn-main"><i class="bi bi-plus-lg"></i> Add Event</a></div>
  <div class="table-responsive"><table class="table table-hover bg-white align-middle">
    <tr><th></th><th>Title</th><th>Category</th><th>Date</th><th>By</th><th>Status</th><th>Actions</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= img_tag($r['image'], $r['title'], 'width="60" height="40" style="object-fit:cover" class="rounded"', event_fallback($r['id'])) ?></td>
      <td><?= e($r['title']) ?> <?= $r['is_featured'] ? '<i class="bi bi-star-fill text-warning"></i>' : '' ?></td>
      <td><?= e($r['cat']) ?></td><td><?= $r['event_date'] ?></td><td><?= e($r['author']) ?></td>
      <td><span class="badge bg-<?= ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'][$r['status']] ?>"><?= $r['status'] ?></span></td>
      <td>
        <form method="post" class="d-flex gap-1" onsubmit="return confirm('Are you sure?')">
          <input type="hidden" name="id" value="<?= $r['id'] ?>">
          <?php if ($r['status'] != 'approved'): ?><button name="do" value="approve" class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check-lg"></i></button><?php endif; ?>
          <?php if ($r['status'] != 'rejected'): ?><button name="do" value="reject" class="btn btn-sm btn-secondary" title="Reject"><i class="bi bi-x-lg"></i></button><?php endif; ?>
          <button name="do" value="feature" class="btn btn-sm btn-warning" title="Feature / Unfeature"><i class="bi bi-star"></i></button>
          <a href="event_form.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary" title="Edit"><i class="bi bi-pencil"></i></a>
          <?php if (is_admin()): ?><button name="do" value="delete" class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash"></i></button><?php endif; ?>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table></div>
</div>
<?php include '../includes/footer.php'; ?>
