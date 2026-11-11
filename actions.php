<?php
// RSVP, Save aur Review yahan handle hote hain
require 'includes/functions.php';
require_login();
$do = $_POST['do'] ?? ''; $eid = (int)($_POST['event_id'] ?? 0); $uid = $_SESSION['user']['id'];
$rows = get_events("AND e.id=?", [$eid]);
if (!$rows) redirect('events.php');
$ev = $rows[0];

if ($do == 'rsvp' || $do == 'save') {
    $table = $do == 'rsvp' ? 'rsvps' : 'favorites';
    $s = $pdo->prepare("SELECT id FROM $table WHERE user_id=? AND event_id=?"); $s->execute([$uid, $eid]);
    if ($s->fetch()) {
        $pdo->prepare("DELETE FROM $table WHERE user_id=? AND event_id=?")->execute([$uid, $eid]);
        flash($do == 'rsvp' ? 'Registration cancelled.' : 'Removed from saved events.', 'warning');
        log_action($do . '_remove', $ev['title']);
    } else {
        $pdo->prepare("INSERT INTO $table (user_id,event_id) VALUES (?,?)")->execute([$uid, $eid]);
        if ($do == 'rsvp') {
            flash('Registration confirmed! A confirmation was added to your notifications.');
            notify($uid, 'Registration confirmed: ' . $ev['title'] . ' on ' . date_text($ev['event_date'], $ev['event_time']) . ' at ' . $ev['venue'] . '.', 'event.php?id=' . $eid);
        } else flash('Event saved to your profile.');
        log_action($do . '_add', $ev['title']);
    }
}
if ($do == 'review' && setting('reviews_enabled') == '1' && (setting('review_after_event', '0') != '1' || $ev['event_date'] <= date('Y-m-d'))) {
    $r = $pdo->prepare("SELECT id FROM rsvps WHERE user_id=? AND event_id=?"); $r->execute([$uid, $eid]);
    $d = $pdo->prepare("SELECT id FROM reviews WHERE user_id=? AND event_id=?"); $d->execute([$uid, $eid]);
    if ($r->fetch() && !$d->fetch()) {
        $pdo->prepare("INSERT INTO reviews (user_id,event_id,rating,comment) VALUES (?,?,?,?)")
            ->execute([$uid, $eid, max(1, min(5, (int)$_POST['rating'])), $_POST['comment']]);
        flash('Thanks for your review!'); log_action('review_add', $ev['title']);
        redirect('event.php?id=' . $eid . '#reviews');
    }
}
redirect('event.php?id=' . $eid);
