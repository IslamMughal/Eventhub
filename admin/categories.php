<?php
require '../includes/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $do = $_POST['do'];
    try {
        if ($do == 'add')  $pdo->prepare("INSERT INTO categories (name,color) VALUES (?,?)")->execute([$_POST['name'], $_POST['color']]);
        if ($do == 'edit') $pdo->prepare("UPDATE categories SET name=?, color=? WHERE id=?")->execute([$_POST['name'], $_POST['color'], (int)$_POST['id']]);
        if ($do == 'delete') $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([(int)$_POST['id']]);
        log_action('category_' . $do, $_POST['name'] ?? ('#' . ($_POST['id'] ?? '')));
        flash('Category saved.');
    } catch (PDOException $ex) { flash('Cannot delete: this category has events.', 'danger'); }
    redirect('categories.php');
}
$cats = get_categories(); $page_title = 'Categories'; $active = 'admin';
include '../includes/header.php'; include 'menu.php';
?>
<div class="container">
  <h4>Categories</h4>
  <form method="post" class="row g-2 mb-3">
    <input type="hidden" name="do" value="add">
    <div class="col-md-4"><input class="form-control" name="name" placeholder="New category name" required></div>
    <div class="col-auto"><input type="color" class="form-control form-control-color" name="color" value="#6f42c1"></div>
    <div class="col-auto"><button class="btn btn-main">Add</button></div>
  </form>
  <?php foreach ($cats as $c): ?>
    <form method="post" class="row g-2 mb-2">
      <input type="hidden" name="id" value="<?= $c['id'] ?>">
      <div class="col-md-4"><input class="form-control" name="name" value="<?= e($c['name']) ?>"></div>
      <div class="col-auto"><input type="color" class="form-control form-control-color" name="color" value="<?= e($c['color']) ?>"></div>
      <div class="col-auto">
        <button name="do" value="edit" class="btn btn-primary">Save</button>
        <button name="do" value="delete" class="btn btn-danger" onclick="return confirm('Delete?')">Delete</button>
      </div>
    </form>
  <?php endforeach; ?>
</div>
<?php include '../includes/footer.php'; ?>
