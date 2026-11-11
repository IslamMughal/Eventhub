<?php
require '../includes/functions.php';
require_admin();
$keys = ['api_enabled' => 'Public JSON API enabled (api/events.php)',
         'show_past_events' => 'Show past events to visitors',
         'allow_submissions' => 'Allow registered users to submit events',
         'reviews_enabled' => 'Allow reviews and ratings',
         'review_after_event' => 'Reviews only after the event date has passed (off = registered users can review right after RSVP)'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($keys as $k => $label) {
        $pdo->prepare("INSERT INTO settings (skey,svalue) VALUES (?,?) ON DUPLICATE KEY UPDATE svalue=VALUES(svalue)")->execute([$k, isset($_POST[$k]) ? '1' : '0']);
    }
    log_action('settings_update'); flash('Settings saved.'); redirect('settings.php');
}
$page_title = 'Settings'; $active = 'admin';
include '../includes/header.php'; include 'menu.php';
?>
<div class="container" style="max-width:700px">
  <h4>Settings: API access &amp; content visibility</h4>
  <form method="post" class="card card-body shadow-sm mb-3">
    <?php foreach ($keys as $k => $label): ?>
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" name="<?= $k ?>" id="<?= $k ?>" <?= setting($k, $k == 'review_after_event' ? '0' : '1') == '1' ? 'checked' : '' ?>>
        <label class="form-check-label" for="<?= $k ?>"><?= $label ?></label>
      </div>
    <?php endforeach; ?>
    <button class="btn btn-main align-self-start">Save Settings</button>
  </form>
  <div class="card card-body">
    <strong>API endpoints (read-only, public)</strong>
    <code><?= BASE_URL ?>api/events.php</code>
    <code><?= BASE_URL ?>api/events.php?id=1</code>
    <code><?= BASE_URL ?>api/events.php?q=music&amp;city=Lahore&amp;when=upcoming</code>
    <a class="mt-2" target="_blank" href="<?= BASE_URL ?>api/events.php">Open API in new tab</a>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
