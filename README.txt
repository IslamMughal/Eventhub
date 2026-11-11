EVENT HUB - PHP + MySQL + Bootstrap 5   (v4 - Neon Noir sidebar layout)
================================================================
SETUP (XAMPP)
1. Folder "event_hub" ko  C:\xampp\htdocs\  me copy karo.
2. XAMPP me Apache + MySQL start karo.
3. http://localhost/phpmyadmin -> Import -> database.sql select -> Go. (creates database citypulse_db)
4. Browser: http://localhost/event_hub/
   (Folder ka naam alag rakha to config/db.php me BASE_URL change karo.)
5. Images online se aati hain (internet chahiye). Net na ho to local default image dikhti hai.

LOGIN (demo)
  Admin     : admin@citypulse.pk / admin123
  Moderator : mod@citypulse.pk   / mod123      (sirf events approve/reject/feature)
  User      : sara@citypulse.pk   / user123

FILE MAP
  index.php          Home: Featured cards + map (card hover = marker highlight)
  events.php         Map + List + Filters + Search (AJAX, bina reload) + Pagination
  event.php          Detail: gallery, mini map, directions, share, RSVP, save, reviews
  actions.php        RSVP / Save / Review handler
  categories.php     Sab categories (d2 style)
  register.php  login.php  logout.php
  profile.php        RSVPs, Saved, My submissions, Notifications, Edit profile
  submit_event.php   User event submit (admin approval ke baad live)
  api/events.php     Public read-only JSON API
  admin/             index (analytics charts), events, event_form, categories, users (roles),
                     settings (API + visibility), logs (activity logs)
  includes/          functions.php (sab helper), header, footer, event_card, event_list, event_form
  assets/css/style.css   Poora design. File ke upar index hai: [HEADER] [FOOTER] [BUTTONS] [CARDS] wagaira search karo
  assets/js/map.js       Map + marker highlight ka code
  config/db.php          Database settings
  images/            Local images (fallback)      uploads/   Upload ki hui images

RANG BADALNA
  assets/css/style.css me  :root  ke andar  --primary  aur  --primary-dark  badlo (koi bhi hex color)

FOOTER KA EMAIL / PHONE / ADDRESS
  config/db.php me SITE_EMAIL, SITE_PHONE, SITE_ADDRESS badlo.

FRONT-END
  Design d2 (EventPulse) project ka hai: navbar, hero, cards, categories page, footer, login/register, profile.
  Bootstrap, Leaflet, Bootstrap Icons, Chart.js ab assets/vendor/ me local hain (net ke baghair bhi chalte hain).

IMAGE BADALNA
  Admin > Events > Edit: file upload karo ya online image URL paste karo (main + gallery).
  Uploaded images automatic resize (max 1200px) + compress hoti hain.
