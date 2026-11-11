-- CityPulse Events database  (phpMyAdmin > Import)
-- Database name: citypulse_db   (config/db.php already points to it)
CREATE DATABASE IF NOT EXISTS citypulse_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE citypulse_db;

DROP TABLE IF EXISTS activity_logs, notifications, settings, reviews, favorites, rsvps, event_images, events, categories, users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','moderator','admin') DEFAULT 'user',
  status ENUM('active','inactive') DEFAULT 'active',
  avatar VARCHAR(255) DEFAULT 'images/avatar.png',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  color VARCHAR(10) DEFAULT '#6f42c1'
);

CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  category_id INT NOT NULL,
  short_desc VARCHAR(255),
  description TEXT,
  venue VARCHAR(150),
  address VARCHAR(255),
  city VARCHAR(80),
  lat DECIMAL(10,7) NOT NULL,
  lng DECIMAL(10,7) NOT NULL,
  event_date DATE NOT NULL,
  event_time TIME,
  price DECIMAL(10,2) DEFAULT 0,
  organizer_name VARCHAR(120),
  organizer_contact VARCHAR(150),
  image VARCHAR(255) DEFAULT 'images/default.jpg',
  is_featured TINYINT DEFAULT 0,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  created_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE event_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT NOT NULL,
  image VARCHAR(255) NOT NULL,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE rsvps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  event_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (user_id, event_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE favorites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  event_id INT NOT NULL,
  UNIQUE (user_id, event_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  event_id INT NOT NULL,
  rating TINYINT NOT NULL,
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  message VARCHAR(255) NOT NULL,
  link VARCHAR(255) DEFAULT '',
  is_read TINYINT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE settings (
  skey VARCHAR(50) PRIMARY KEY,
  svalue VARCHAR(255)
);

CREATE TABLE activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(60) NOT NULL,
  details VARCHAR(255) DEFAULT '',
  ip VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Logins: admin@citypulse.pk / admin123 | mod@citypulse.pk / mod123 | sara@citypulse.pk, bilal@citypulse.pk, hina@citypulse.pk, omar@citypulse.pk / user123
INSERT INTO users (name,email,password,role,status) VALUES
('Admin CityPulse','admin@citypulse.pk','$2y$10$093WpkkIPqxkx6.5zjNehO5hkyhib6COxPDv8yT3kewolh3FPuD4i','admin','active'),
('Moderator Zain','mod@citypulse.pk','$2y$10$.o6CNWkAtqkUasjRbm9X2uhhA2z8A/ZmsuCq7nZFZDWCR0YMsXEHG','moderator','active'),
('Sara Ahmed','sara@citypulse.pk','$2y$10$CNov9sy29HCXFFWlFNdppe9dny2eHLVbPxsKeUif5aBFjiBgfzU46','user','active'),
('Bilal Raza','bilal@citypulse.pk','$2y$10$CNov9sy29HCXFFWlFNdppe9dny2eHLVbPxsKeUif5aBFjiBgfzU46','user','active'),
('Hina Malik','hina@citypulse.pk','$2y$10$CNov9sy29HCXFFWlFNdppe9dny2eHLVbPxsKeUif5aBFjiBgfzU46','user','active'),
('Omar Farooq','omar@citypulse.pk','$2y$10$CNov9sy29HCXFFWlFNdppe9dny2eHLVbPxsKeUif5aBFjiBgfzU46','user','active');

INSERT INTO categories (name,color) VALUES
('Music','#fb7185'),
('Sports','#4ade80'),
('Technology','#60a5fa'),
('Art & Culture','#c084fc'),
('Food & Drink','#fb923c'),
('Health & Wellness','#2dd4bf'),
('Community Service','#a3e635');

INSERT INTO settings (skey,svalue) VALUES
('api_enabled','1'),
('show_past_events','1'),
('allow_submissions','1'),
('reviews_enabled','1'),
('review_after_event','0');

-- Live images: each event uses an online photo matching its theme (internet needed). If a link fails, the local fallback image shows.
INSERT INTO events (title,category_id,short_desc,description,venue,address,city,lat,lng,event_date,event_time,price,organizer_name,organizer_contact,image,is_featured,status,created_by) VALUES
('Faisalabad AI & Robotics Expo',3,'Robots, drones and AI demos from Pakistani engineers.','Two days of live robotics demos, student project showcases and talks on applied AI for textile and agriculture. Includes a hands-on drone zone.','Expo Centre Faisalabad','Jaranwala Road','Faisalabad',31.4504,73.135,'2026-11-14','10:00:00',300,'TechPunjab Labs','expo@techpunjab.pk','https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&w=800&h=500&q=80',1,'approved',1),
('Multan Sufi Music Festival',1,'Kafi, qawwali and folk music in the City of Saints.','An open-air festival with Saraiki folk singers, kafi recitals and a candle-lit closing set. Blue-pottery stalls on site.','Qasim Bagh Stadium','Qasim Bagh','Multan',30.1575,71.5249,'2026-12-05','18:30:00',700,'Saraiki Sounds','tickets@saraikisounds.pk','https://images.unsplash.com/photo-1540039155733-5bb30b53aa14?auto=format&fit=crop&w=800&h=500&q=80',1,'approved',1),
('Sialkot Football League Final',2,'The grand final of the Sialkot inter-club league.','Top two clubs fight for the trophy. Food stalls, a youth skills clinic before kick-off and a prize ceremony after the match.','Jinnah Stadium','Kashmir Road','Sialkot',32.4945,74.5229,'2026-10-11','16:00:00',200,'Sialkot Sports Board','league@sialkotsports.pk','https://loremflickr.com/800/500/football?lock=130',0,'approved',1),
('Peshawar Barbecue & Karahi Fest',5,'Charsi tikka, chapli kebab and kahwa under the lights.','A winter food night with 40 stalls serving Peshawari classics, live rubab music and a kahwa-tasting corner for families.','Nishtar Hall Lawn','Khyber Road','Peshawar',34.0151,71.5249,'2026-11-21','17:00:00',250,'Khyber Foodies','hello@khyberfoodies.pk','https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=800&h=500&q=80',1,'approved',1),
('Rawalpindi Street Photography Walk',4,'A guided photo walk through Raja Bazaar and Saddar.','Learn composition and storytelling from working photographers while exploring old city streets. Bring any camera or phone. Best photos are exhibited.','Raja Bazaar Gate','Raja Bazaar','Rawalpindi',33.6007,73.0679,'2026-11-29','08:00:00',0,'Lens Collective PK','walk@lenscollective.pk','https://loremflickr.com/800/500/photography?lock=150',0,'approved',1),
('Murree Hills Clean-up Drive',7,'Volunteers clear trails and viewpoints of litter.','Join a one-day clean-up along Murree''s trails. Gloves, bags and a hot lunch provided. Families and student groups are welcome.','Mall Road Viewpoint','Mall Road','Murree',33.907,73.3943,'2026-10-18','09:00:00',0,'Clean Hills Pakistan','volunteer@cleanhills.pk','https://loremflickr.com/800/500/mountain?lock=160',0,'approved',1),
('Hyderabad Ajrak & Handicraft Fair',4,'Sindhi ajrak, pottery and embroidery from 80 artisans.','Meet craftspeople, watch live block-printing demos and shop directly from artisans. Includes a children''s pottery corner.','Sindh Museum Grounds','Autobahn Road','Hyderabad',25.396,68.3578,'2026-12-12','11:00:00',0,'Sindh Crafts Guild','fair@sindhcrafts.pk','https://loremflickr.com/800/500/handicraft?lock=170',0,'approved',1),
('Lahore Cyclothon 2026',2,'A 40 km charity cycling ride across Lahore.','Ride from Minar-e-Pakistan to Ravi Bridge and back. Helmets mandatory, hydration points every 8 km, finisher medals for all.','Greater Iqbal Park','Circular Road','Lahore',31.5925,74.3095,'2026-11-15','06:30:00',600,'Pedal Lahore','ride@pedallahore.pk','https://loremflickr.com/800/500/cycling?lock=180',1,'approved',1),
('Karachi Cyber Security Summit',3,'Ethical hacking talks and a capture-the-flag contest.','Industry speakers cover cloud security, phishing defence and incident response. Evening CTF with prizes for student teams.','Expo Centre Karachi','University Road','Karachi',24.9056,67.0822,'2027-01-16','09:30:00',1200,'SecureSindh','summit@securesindh.pk','https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=800&h=500&q=80',0,'approved',1),
('Margalla Mindfulness Sunday',6,'Sunrise meditation and breathing session in the hills.','A guided open-air session of breathwork, gentle stretching and silent walking, followed by herbal tea and a short talk on sleep and stress.','Trail 3 Trailhead','Margalla Hills','Islamabad',33.738,73.064,'2026-11-08','06:45:00',0,'Calm Capital','hello@calmcapital.pk','https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=800&h=500&q=80',0,'approved',1),
('Gujranwala Blood Donation Camp',7,'Donate blood and help local hospitals.','Certified staff run the camp with free basic screening and refreshments. Every donor receives a certificate.','Municipal Stadium Hall','Sheranwala Bagh Road','Gujranwala',32.1617,74.1883,'2026-11-03','10:00:00',0,'Red Crescent Volunteers','camp@redcrescentgrw.pk','https://loremflickr.com/800/500/hospital?lock=210',0,'approved',1),
('Lahore Coffee & Dessert Carnival',5,'Specialty coffee, bakers and dessert pop-ups.','Taste single-origin brews, join a latte-art class and vote for the best dessert stall. Live acoustic sets in the afternoon.','Packages Mall Atrium','Walton Road','Lahore',31.4697,74.3673,'2027-01-24','14:00:00',350,'Brew & Bake Lahore','carnival@brewbake.pk','https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=800&h=500&q=80',0,'approved',1),
('Bahawalpur Classical Dance Night',4,'Kathak and folk dance in the Noor Mahal gardens.','Professional troupes perform kathak, bhangra and Saraiki jhumar. Short workshop for beginners before the show.','Noor Mahal Gardens','Noor Mahal Road','Bahawalpur',29.3956,71.6836,'2026-12-19','19:00:00',500,'Cholistan Arts Trust','dance@cholistanarts.pk','https://loremflickr.com/800/500/dance?lock=230',0,'approved',1),
('Abbottabad Trail Marathon',2,'21K and 10K mountain trail run in the Galiyat foothills.','Scenic forest-trail race with aid stations, timing chips and finisher medals. Post-race breakfast included.','Ayubia Trailhead','Galiyat Road','Abbottabad',34.064,73.398,'2027-02-07','06:00:00',800,'Hazara Runners','run@hazararunners.pk','https://loremflickr.com/800/500/running?lock=240',1,'approved',1);

INSERT INTO event_images (event_id,image) VALUES
(1,'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=800&h=500&q=80'),
(1,'https://loremflickr.com/800/500/circuit?lock=112'),
(2,'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=800&h=500&q=80'),
(2,'https://loremflickr.com/800/500/stage?lock=122'),
(3,'https://loremflickr.com/800/500/stadium?lock=131'),
(3,'https://loremflickr.com/800/500/soccer?lock=132'),
(4,'https://loremflickr.com/800/500/kebab?lock=141'),
(4,'https://loremflickr.com/800/500/grill?lock=142'),
(5,'https://loremflickr.com/800/500/camera?lock=151'),
(5,'https://loremflickr.com/800/500/street?lock=152'),
(6,'https://loremflickr.com/800/500/forest?lock=161'),
(6,'https://loremflickr.com/800/500/volunteer?lock=162'),
(7,'https://loremflickr.com/800/500/pottery?lock=171'),
(7,'https://loremflickr.com/800/500/textile?lock=172'),
(8,'https://loremflickr.com/800/500/bicycle?lock=181'),
(8,'https://loremflickr.com/800/500/race?lock=182'),
(9,'https://loremflickr.com/800/500/server?lock=191'),
(9,'https://loremflickr.com/800/500/keyboard?lock=192'),
(10,'https://loremflickr.com/800/500/yoga?lock=201'),
(10,'https://loremflickr.com/800/500/sunrise?lock=202'),
(11,'https://loremflickr.com/800/500/nurse?lock=211'),
(11,'https://loremflickr.com/800/500/medical?lock=212'),
(12,'https://loremflickr.com/800/500/dessert?lock=221'),
(12,'https://loremflickr.com/800/500/bakery?lock=222'),
(13,'https://loremflickr.com/800/500/theatre?lock=231'),
(13,'https://loremflickr.com/800/500/costume?lock=232'),
(14,'https://loremflickr.com/800/500/trail?lock=241'),
(14,'https://loremflickr.com/800/500/athlete?lock=242');

INSERT INTO rsvps (user_id,event_id) VALUES
(3,1),(3,2),(3,8),(4,2),(4,4),(4,10),(5,5),(5,7),(5,13),(6,8),(6,14),(3,6),(4,11),(5,3);

INSERT INTO favorites (user_id,event_id) VALUES
(3,2),(3,14),(4,4),(4,8),(5,13),(5,5),(6,9),(6,1);

INSERT INTO reviews (user_id,event_id,rating,comment) VALUES
(3,6,5,'Well organised clean-up, we filled over 80 bags. Will join again!'),
(4,6,4,'Great cause and friendly volunteers. Lunch was a nice touch.'),
(5,3,5,'What a final! Packed stadium and an amazing atmosphere.'),
(6,3,4,'Good match, entry was a bit slow but worth it.'),
(3,10,5,'Perfect way to start a Sunday. Very calming.'),
(4,4,5,'The chapli kebabs were unreal, best food event this year.'),
(5,12,4,'Lovely coffee selection and the latte-art class was fun.'),
(6,11,5,'Smooth process and caring staff. Proud to donate.');

INSERT INTO notifications (user_id,message,link,is_read) VALUES
(3,'Welcome to CityPulse Events! Your RSVPs and updates will appear here.','profile.php',0),
(3,'You are registered for Faisalabad AI & Robotics Expo.','event.php?id=1',0),
(4,'Peshawar Barbecue & Karahi Fest starts soon - see you there!','event.php?id=4',0),
(5,'New event added near you: Rawalpindi Street Photography Walk.','event.php?id=5',1),
(6,'Thanks for reviewing Sialkot Football League Final.','event.php?id=3',1),
(2,'3 new event submissions are waiting for approval.','admin/events.php',0);

INSERT INTO activity_logs (user_id,action,details,ip) VALUES
(1,'setup','Database citypulse_db created with demo data','127.0.0.1'),
(1,'event_create','Published Faisalabad AI & Robotics Expo','127.0.0.1'),
(1,'event_create','Published Multan Sufi Music Festival','127.0.0.1'),
(2,'event_approve','Approved Lahore Cyclothon 2026','127.0.0.1'),
(3,'rsvp','RSVP for Faisalabad AI & Robotics Expo','127.0.0.1'),
(4,'review','Reviewed Peshawar Barbecue & Karahi Fest','127.0.0.1'),
(5,'favorite','Saved Rawalpindi Street Photography Walk','127.0.0.1'),
(6,'login','User logged in','127.0.0.1');
