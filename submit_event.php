<?php
require 'includes/functions.php';
require_login();
if (setting('allow_submissions') != '1') { flash('Event submissions are currently closed.', 'warning'); redirect('index.php'); }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    save_event(null, false);   // status = pending
    flash('Event submitted! It will appear after admin approval.'); redirect('profile.php');
}
$ev = ['title' => '', 'category_id' => 0, 'short_desc' => '', 'description' => '', 'venue' => '', 'address' => '', 'city' => '',
       'lat' => '', 'lng' => '', 'event_date' => '', 'event_time' => '', 'price' => 0, 'organizer_name' => $_SESSION['user']['name'],
       'organizer_contact' => $_SESSION['user']['email'], 'status' => 'pending', 'is_featured' => 0];
$admin = false; $page_title = 'Submit Event'; $active = 'submit';
include 'includes/header.php';
?>
<section class="events-hero"><div class="container">
  <h1 style="font-size:2.5rem;font-weight:800;margin-bottom:0.5rem">Submit an Event</h1>
  <p style="color:var(--text-secondary);font-size:1.125rem;margin:0">Your event will go live after admin approval.</p>
</div></section>
<div class="container my-4"><?php include 'includes/event_form.php'; ?></div>
<?php include 'includes/footer.php'; ?>
