<?php
// Har page sab se pehle ye file include karta hai
session_start();
require_once __DIR__ . '/../config/db.php';

// ---------- Basic helpers ----------
function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }   // XSS se bachao
function redirect($url) { header('Location: ' . $url); exit; }
function is_logged() { return isset($_SESSION['user']); }
function is_admin()  { return is_logged() && $_SESSION['user']['role'] == 'admin'; }
function is_staff()  { return is_logged() && in_array($_SESSION['user']['role'], ['admin', 'moderator']); }
function require_login() { if (!is_logged()) redirect(BASE_URL . 'login.php'); }
function require_admin() { if (!is_admin())  redirect(BASE_URL . 'login.php'); }
function require_staff() { if (!is_staff())  redirect(BASE_URL . 'login.php'); }

// Flash message (ek baar dikhta hai)
function flash($msg, $type = 'success') { $_SESSION['flash'] = [$msg, $type]; }
function show_flash() {
    if (isset($_SESSION['flash'])) {
        [$m, $t] = $_SESSION['flash'];
        echo "<div class='alert alert-$t alert-dismissible fade show'>" . e($m) . "<button class='btn-close' data-bs-dismiss='alert'></button></div>";
        unset($_SESSION['flash']);
    }
}

// ---------- Icons (d2 wale SVG) + category icon/description ----------
function svg($n) {
    $p = [
     'cal'   => '<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>',
     'clock' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>',
     'pin'   => '<path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>',
     'users' => '<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>',
     'mail'  => '<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>',
     'user'  => '<path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 2a6 6 0 110 12 6 6 0 010-12zm0 2a2 2 0 100 4 2 2 0 000-4z"/>',
     'logout'=> '<path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>',
     'search'=> '<path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>',
    ];
    return '<svg viewBox="0 0 20 20" fill="currentColor">' . ($p[$n] ?? '') . '</svg>';
}
function cat_icon($name) {
    $i = ['music' => '🎵', 'sports' => '⚽', 'technology' => '💻', 'art & culture' => '🎨', 'food & drink' => '🍕',
          'health & wellness' => '🧘', 'community service' => '🤝', 'community' => '🤝'];
    return $i[strtolower($name)] ?? '📅';
}
function cat_desc($name) {
    $d = ['music' => 'Live concerts, festivals, and musical performances', 'sports' => 'Tournaments, matches, and athletic competitions',
          'technology' => 'Tech meetups, workshops, and hackathons', 'art & culture' => 'Exhibitions, theater, and cultural celebrations',
          'food & drink' => 'Food festivals, tastings, and culinary events', 'health & wellness' => 'Yoga, fitness, and healthy living events',
          'community service' => 'Volunteering drives and community gatherings'];
    return $d[strtolower($name)] ?? 'Discover events in this category';
}
// Categories + approved events ki ginti
function get_categories_with_counts() {
    global $pdo;
    return $pdo->query("SELECT c.*, COUNT(e.id) AS total FROM categories c
        LEFT JOIN events e ON e.category_id=c.id AND e.status='approved' GROUP BY c.id, c.name, c.color ORDER BY c.name")->fetchAll();
}

function price_text($p) { return $p > 0 ? 'Rs. ' . number_format($p) : 'Free'; }
function date_text($d, $t) { return date('d M Y', strtotime($d)) . ($t ? ', ' . date('h:i A', strtotime($t)) : ''); }

// ---------- Images (local path ya online URL, dono chalte hain) ----------
function img_src($p) { return preg_match('#^https?://#', $p) ? $p : BASE_URL . $p; }
// Online image na khule to $fallback (local image) dikhti hai
function img_tag($path, $alt, $attrs = '', $fallback = 'images/default.jpg') {
    return '<img src="' . e(img_src($path)) . '" alt="' . e($alt) . '" loading="lazy" ' . $attrs .
           ' onerror="this.onerror=null;this.src=\'' . BASE_URL . $fallback . '\'">';
}
// Event ki apni local image (images/event5.jpg), na ho to default
function event_fallback($id) { return file_exists(__DIR__ . "/../images/event$id.jpg") ? "images/event$id.jpg" : 'images/default.jpg'; }

// ---------- Settings (admin panel se control) ----------
function setting($key, $default = '1') {
    global $pdo; static $all = null;
    if ($all === null) $all = $pdo->query("SELECT skey, svalue FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
    return $all[$key] ?? $default;
}

// ---------- Activity log ----------
function log_action($action, $details = '') {
    global $pdo;
    $pdo->prepare("INSERT INTO activity_logs (user_id,action,details,ip) VALUES (?,?,?,?)")
        ->execute([$_SESSION['user']['id'] ?? null, $action, substr($details, 0, 250), $_SERVER['REMOTE_ADDR'] ?? '']);
}

// ---------- Notification (site ke andar) + email ----------
function notify($user_id, $msg, $link = '') {
    global $pdo;
    $pdo->prepare("INSERT INTO notifications (user_id,message,link) VALUES (?,?,?)")->execute([$user_id, $msg, $link]);
    $s = $pdo->prepare("SELECT email FROM users WHERE id=?"); $s->execute([$user_id]);
    if ($mail = $s->fetchColumn()) @mail($mail, SITE_NAME . ' notification', $msg, 'From: no-reply@eventify.local'); // XAMPP me mail setup ho to jaye gi
}

// ---------- Events ----------
function get_events($extra = '', $params = []) {
    global $pdo;
    $st = $pdo->prepare("SELECT e.*, c.name AS cat, c.color FROM events e
        JOIN categories c ON c.id = e.category_id WHERE e.status='approved' $extra");
    $st->execute($params);
    return $st->fetchAll();
}
function get_categories() { global $pdo; return $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(); }

// Search + filters (events page, AJAX aur API teeno yehi use karte hain)
function search_events($g) {
    $sql = ''; $p = [];
    $q = trim($g['q'] ?? ''); $city = trim($g['city'] ?? ''); $when = $g['when'] ?? '';
    $cats = array_filter((array)($g['cat'] ?? []), 'is_numeric');
    if ($q !== '')  { $sql .= " AND (e.title LIKE ? OR c.name LIKE ? OR e.organizer_name LIKE ? OR e.venue LIKE ?)"; $p = array_fill(0, 4, "%$q%"); }
    if ($cats)      { $sql .= " AND e.category_id IN (" . implode(',', array_fill(0, count($cats), '?')) . ")"; $p = array_merge($p, array_values($cats)); }
    if (!empty($g['from'])) { $sql .= " AND e.event_date >= ?"; $p[] = $g['from']; }
    if (!empty($g['to']))   { $sql .= " AND e.event_date <= ?"; $p[] = $g['to']; }
    if ($city !== '') { $sql .= " AND e.city LIKE ?"; $p[] = "%$city%"; }
    if ($when == 'upcoming') $sql .= " AND e.event_date >= CURDATE()";
    if ($when == 'past')     $sql .= " AND e.event_date < CURDATE()";
    if (setting('show_past_events') != '1') $sql .= " AND e.event_date >= CURDATE()";
    return get_events($sql . " ORDER BY (e.event_date < CURDATE()), e.event_date", $p);   // pehle upcoming, phir past
}

// Map ke liye chota data
function events_for_map($events) {
    $js = [];
    foreach ($events as $ev) $js[] = ['id' => $ev['id'], 'title' => e($ev['title']), 'cat' => e($ev['cat']), 'color' => $ev['color'],
        'date' => date_text($ev['event_date'], $ev['event_time']), 'lat' => (float)$ev['lat'], 'lng' => (float)$ev['lng']];
    return $js;
}

// ---------- Image upload (resize + compress = image optimization) ----------
function upload_image($file, $max = 1200) {
    if (!$file || $file['error'] !== 0 || $file['size'] > 8 * 1024 * 1024) return null;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) return null;
    if (function_exists('imagecreatefromstring') && function_exists('imagejpeg')) {
        $img = @imagecreatefromstring(file_get_contents($file['tmp_name']));
        if ($img) {
            $w = imagesx($img); $h = imagesy($img);
            if ($w > $max) {                                   // bari image ko chota karo
                $nh = (int)($h * $max / $w); $n = imagecreatetruecolor($max, $nh);
                imagefill($n, 0, 0, imagecolorallocate($n, 255, 255, 255));
                imagecopyresampled($n, $img, 0, 0, 0, 0, $max, $nh, $w, $h); $img = $n;
            }
            $path = 'uploads/' . uniqid() . '.jpg';
            imagejpeg($img, __DIR__ . '/../' . $path, 82);      // quality 82 = chhoti file
            return $path;
        }
    }
    $path = 'uploads/' . uniqid() . '.' . $ext;                 // GD na ho to seedha save
    move_uploaded_file($file['tmp_name'], __DIR__ . '/../' . $path);
    return $path;
}

// ---------- Event save (add/edit) - user submit aur admin dono ----------
function save_event($id = null, $admin = false) {
    global $pdo; $f = $_POST;
    $url = trim($f['image_url'] ?? '');
    $img = upload_image($_FILES['image'] ?? null) ?: (preg_match('#^https?://#', $url) ? $url : null);
    $v = [$f['title'], (int)$f['category_id'], $f['short_desc'], $f['description'], $f['venue'], $f['address'],
          $f['city'], (float)$f['lat'], (float)$f['lng'], $f['event_date'], ($f['event_time'] ?? '') ?: null, (float)$f['price'],
          $f['organizer_name'], $f['organizer_contact']];
    $cols = "title=?,category_id=?,short_desc=?,description=?,venue=?,address=?,city=?,lat=?,lng=?,event_date=?,event_time=?,price=?,organizer_name=?,organizer_contact=?";
    if ($id) {
        if ($img) { $cols .= ",image=?"; $v[] = $img; }
        if ($admin) { $cols .= ",status=?,is_featured=?"; $v[] = $f['status']; $v[] = isset($f['is_featured']) ? 1 : 0; }
        $v[] = $id;
        $pdo->prepare("UPDATE events SET $cols WHERE id=?")->execute($v);
        log_action('event_edit', "Event #$id: " . $f['title']);
    } else {
        $cols .= ",image=?,status=?,is_featured=?,created_by=?";
        $v[] = $img ?: 'images/default.jpg';
        $v[] = $admin ? $f['status'] : 'pending';
        $v[] = ($admin && isset($f['is_featured'])) ? 1 : 0;
        $v[] = $_SESSION['user']['id'];
        $pdo->prepare("INSERT INTO events SET $cols")->execute($v);
        $id = $pdo->lastInsertId();
        log_action($admin ? 'event_add' : 'event_submit', "Event #$id: " . $f['title']);
    }
    // extra gallery images (upload ya URL)
    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['name'] as $i => $n) {
            $p = upload_image(['name' => $n, 'tmp_name' => $_FILES['gallery']['tmp_name'][$i], 'error' => $_FILES['gallery']['error'][$i], 'size' => $_FILES['gallery']['size'][$i]]);
            if ($p) $pdo->prepare("INSERT INTO event_images (event_id,image) VALUES (?,?)")->execute([$id, $p]);
        }
    }
    foreach (preg_split('/\r?\n/', trim($f['gallery_urls'] ?? '')) as $u) {
        $u = trim($u);
        if (preg_match('#^https?://#', $u)) $pdo->prepare("INSERT INTO event_images (event_id,image) VALUES (?,?)")->execute([$id, $u]);
    }
    return $id;
}
