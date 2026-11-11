// Map ka sara kaam yahan (events.php aur index.php dono use karte hain)
var EventMap = {
  map: null, markers: {},

  init: function (id) {
    this.map = L.map(id).setView([30.4, 71.5], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; OpenStreetMap'}).addTo(this.map);
  },

  // Rang wala marker (selected ho to bada)
  icon: function (color, big) {
    var s = big ? 34 : 22;
    return L.divIcon({className: '', iconSize: [s, s], iconAnchor: [s / 2, s / 2],
      html: '<div style="background:' + color + ';width:' + s + 'px;height:' + s + 'px;border-radius:50%;border:3px solid #fff;box-shadow:0 0 6px #0007"></div>'});
  },

  // Events ke markers lagao (purane hata do)
  set: function (events) {
    var self = this;
    for (var k in this.markers) this.map.removeLayer(this.markers[k]);
    this.markers = {};
    events.forEach(function (ev) {
      var m = L.marker([ev.lat, ev.lng], {icon: self.icon(ev.color)}).addTo(self.map)
        .bindPopup('<b>' + ev.title + '</b><br>' + ev.date + '<br>' + ev.cat + '<br><a href="event.php?id=' + ev.id + '">View Details</a>');
      m.ev = ev; self.markers[ev.id] = m;
      m.on('click', function () { self.select(ev.id); });
    });
    if (events.length) this.map.fitBounds(events.map(function (e) { return [e.lat, e.lng]; }), {padding: [40, 40], maxZoom: 12});
  },

  // Marker highlight: bada + popup + zaroorat ho to map hilao
  select: function (id) {
    for (var k in this.markers) this.markers[k].setIcon(this.icon(this.markers[k].ev.color, k == id));
    var m = this.markers[id]; if (!m) return;
    if (!this.map.getBounds().contains(m.getLatLng())) this.map.panTo(m.getLatLng());
    m.openPopup();
  },

  // Cards par hover/click = marker highlight
  bindCards: function (root) {
    var self = this;
    root.querySelectorAll('.event-card').forEach(function (c) {
      c.addEventListener('mouseenter', function () { self.select(c.dataset.id); });
      c.addEventListener('click', function () { self.select(c.dataset.id); });
    });
  }
};
