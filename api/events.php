<?php
// Public read-only JSON API  (Guest = Public role)
//   api/events.php                 -> sab approved events (filters: q, cat[], city, from, to, when)
//   api/events.php?id=3            -> ek event
require '../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
if (setting('api_enabled') != '1') { http_response_code(403); echo json_encode(['error' => 'API is disabled by admin']); exit; }

$events = isset($_GET['id']) ? get_events("AND e.id=?", [(int)$_GET['id']]) : search_events($_GET);
$out = array_map(function ($ev) {
    return ['id' => (int)$ev['id'], 'title' => $ev['title'], 'category' => $ev['cat'], 'short_description' => $ev['short_desc'],
        'description' => $ev['description'], 'venue' => $ev['venue'], 'address' => $ev['address'], 'city' => $ev['city'],
        'lat' => (float)$ev['lat'], 'lng' => (float)$ev['lng'], 'date' => $ev['event_date'], 'time' => $ev['event_time'],
        'price' => (float)$ev['price'], 'organizer' => $ev['organizer_name'], 'contact' => $ev['organizer_contact'],
        'image' => preg_match('#^https?://#', $ev['image']) ? $ev['image'] : 'http://' . $_SERVER['HTTP_HOST'] . BASE_URL . $ev['image'],
        'featured' => (bool)$ev['is_featured']];
}, $events);
echo json_encode(['count' => count($out), 'events' => $out], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
