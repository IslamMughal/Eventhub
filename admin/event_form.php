<?php
require '../includes/functions.php';
require_staff();
$id = (int)($_GET['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Gallery image delete
    if (isset($_POST['del_img'])) { $pdo->prepare("DELETE FROM event_images WHERE id=?")->execute([(int)$_POST['del_img']]); redirect("event_form.php?id=$id"); }
    save_event($id ?: null, true);
    flash('Event saved.'); redirect('events.php');
}
if ($id) { $s = $pdo->prepare("SELECT * FROM events WHERE id=?"); $s->execute([$id]); $ev = $s->fetch(); }
else $ev = ['title' => '', 'category_id' => 0, 'short_desc' => '', 'description' => '', 'venue' => '', 'address' => '', 'city' => '', 'lat' => '', 'lng' => '',
            'event_date' => '', 'event_time' => '', 'price' => 0, 'organizer_name' => '', 'organizer_contact' => '', 'status' => 'approved', 'is_featured' => 0];
$admin = true; $page_title = $id ? 'Edit Event' : 'Add Event'; $active = 'admin';
include '../includes/header.php'; include 'menu.php';
?>
<div class="container mb-4">
  <h4><?= $page_title ?></h4>
  <?php include '../includes/event_form.php'; ?>
  <?php if ($id): $g = $pdo->prepare("SELECT * FROM event_images WHERE event_id=?"); $g->execute([$id]); ?>
    <h6 class="mt-4">Gallery images</h6>
    <form method="post" class="d-flex flex-wrap gap-2">
      <?php foreach ($g->fetchAll() as $im): ?>
        <div class="text-center"><?= img_tag($im['image'], 'Gallery image', 'width="120" class="rounded d-block"') ?>
          <button name="del_img" value="<?= $im['id'] ?>" class="btn btn-sm btn-outline-danger mt-1">Delete</button></div>
      <?php endforeach; ?>
    </form>
  <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
