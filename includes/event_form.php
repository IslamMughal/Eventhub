<?php
// Shared form (user submit + admin add/edit).  Chahiye: $ev (array), $admin (true/false)
$cats = get_categories();
?>
<form method="post" enctype="multipart/form-data" class="card card-body">
  <div class="row g-3">
    <div class="col-md-8"><label class="form-label">Event title</label><input class="form-control" name="title" value="<?= e($ev['title']) ?>" required></div>
    <div class="col-md-4"><label class="form-label">Category</label>
      <select class="form-select" name="category_id"><?php foreach ($cats as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $c['id'] == $ev['category_id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-12"><label class="form-label">Short description</label><input class="form-control" name="short_desc" maxlength="255" value="<?= e($ev['short_desc']) ?>" required></div>
    <div class="col-12"><label class="form-label">Full description</label><textarea class="form-control" rows="4" name="description" required><?= e($ev['description']) ?></textarea></div>
    <div class="col-md-4"><label class="form-label">Venue</label><input class="form-control" name="venue" value="<?= e($ev['venue']) ?>" required></div>
    <div class="col-md-5"><label class="form-label">Street address</label><input class="form-control" name="address" value="<?= e($ev['address']) ?>"></div>
    <div class="col-md-3"><label class="form-label">City</label><input class="form-control" name="city" value="<?= e($ev['city']) ?>" required></div>
    <div class="col-md-3"><label class="form-label">Date</label><input type="date" class="form-control" name="event_date" value="<?= e($ev['event_date']) ?>" required></div>
    <div class="col-md-3"><label class="form-label">Time</label><input type="time" class="form-control" name="event_time" value="<?= e($ev['event_time']) ?>"></div>
    <div class="col-md-3"><label class="form-label">Price (0 = Free)</label><input type="number" min="0" class="form-control" name="price" value="<?= e($ev['price']) ?>"></div>
    <div class="col-md-3"><label class="form-label">Organizer name</label><input class="form-control" name="organizer_name" value="<?= e($ev['organizer_name']) ?>"></div>
    <div class="col-md-6"><label class="form-label">Organizer contact</label><input class="form-control" name="organizer_contact" value="<?= e($ev['organizer_contact']) ?>"></div>
    <div class="col-md-3"><label class="form-label">Latitude</label><input class="form-control" id="lat" name="lat" value="<?= e($ev['lat']) ?>" required></div>
    <div class="col-md-3"><label class="form-label">Longitude</label><input class="form-control" id="lng" name="lng" value="<?= e($ev['lng']) ?>" required></div>
    <div class="col-12"><label class="form-label">Click on the map to pick the event location</label><div id="pick"></div></div>
    <div class="col-md-6"><label class="form-label">Main image (upload)</label><input type="file" class="form-control" name="image" accept="image/*"></div>
    <div class="col-md-6"><label class="form-label">...or main image URL (online)</label><input type="url" class="form-control" name="image_url" placeholder="https://..."></div>
    <div class="col-md-6"><label class="form-label">More gallery images (upload)</label><input type="file" class="form-control" name="gallery[]" accept="image/*" multiple></div>
    <div class="col-md-6"><label class="form-label">...or gallery image URLs (one per line)</label><textarea class="form-control" rows="2" name="gallery_urls" placeholder="https://..."></textarea></div>
    <?php if ($admin): ?>
      <div class="col-md-4"><label class="form-label">Status</label>
        <select class="form-select" name="status"><?php foreach (['approved', 'pending', 'rejected'] as $s): ?>
          <option <?= $ev['status'] == $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
      <div class="col-md-4 pt-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_featured" id="f" <?= $ev['is_featured'] ? 'checked' : '' ?>>
        <label for="f" class="form-label">Featured on homepage</label></div></div>
    <?php endif; ?>
  </div>
  <button class="btn btn-primary btn-lg mt-3 align-self-start"><?= $admin ? 'Save Event' : 'Submit for Approval' ?></button>
</form>
<script>
var pm = L.map('pick').setView([<?= (float)$ev['lat'] ?: 30.4 ?>, <?= (float)$ev['lng'] ?: 71.5 ?>], <?= $ev['lat'] ? 13 : 5 ?>);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; OpenStreetMap'}).addTo(pm);
var pin = <?= $ev['lat'] ? "L.marker([{$ev['lat']},{$ev['lng']}]).addTo(pm)" : 'null' ?>;
pm.on('click', function (e) {
  if (pin) pin.setLatLng(e.latlng); else pin = L.marker(e.latlng).addTo(pm);
  document.getElementById('lat').value = e.latlng.lat.toFixed(7);
  document.getElementById('lng').value = e.latlng.lng.toFixed(7);
});
</script>
