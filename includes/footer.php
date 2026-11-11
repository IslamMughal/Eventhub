<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <p class="footer-desc"><strong><?= SITE_NAME ?></strong> &mdash; discover what is happening around you, save your favourites and meet your community.</p>
        <div class="social-links d-flex gap-2">
          <a href="#" class="social-link" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" class="social-link" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="social-link" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        </div>
      </div>
      <div class="footer-links">
        <h4>Browse</h4>
        <a href="<?= BASE_URL ?>events.php">All events</a>
        <a href="<?= BASE_URL ?>categories.php">Categories</a>
        <a href="<?= BASE_URL ?><?= is_logged() ? 'profile.php' : 'login.php' ?>"><?= is_logged() ? 'My profile' : 'Login' ?></a>
      </div>
      <div class="footer-links">
        <h4>Popular</h4>
        <?php foreach (array_slice(get_categories(), 0, 4) as $fc): ?>
          <a href="<?= BASE_URL ?>events.php?cat[]=<?= $fc['id'] ?>"><?= e($fc['name']) ?></a>
        <?php endforeach; ?>
      </div>
      <div class="footer-links">
        <h4>Contact</h4>
        <p><i class="bi bi-envelope"></i> <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a></p>
        <p><i class="bi bi-telephone"></i> <?= SITE_PHONE ?></p>
        <p><i class="bi bi-geo-alt"></i> <?= SITE_ADDRESS ?></p>
      </div>
    </div>
    <div class="footer-bottom"><p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p></div>
  </div>
</footer>
</div><!-- /.app-body -->
</div><!-- /.app-shell -->
<script>
(function () {
  var b = document.body, t = document.getElementById('sideToggle'), k = document.getElementById('sideBackdrop');
  if (t) t.addEventListener('click', function () { b.classList.toggle('side-open'); });
  if (k) k.addEventListener('click', function () { b.classList.remove('side-open'); });
})();
</script>
</body>
</html>
