<?php
require 'includes/functions.php';
$page_title = 'Login'; $active = 'login'; $flash_inline = true;
if (is_logged()) redirect('index.php');
$email = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $s = $pdo->prepare("SELECT * FROM users WHERE email=?"); $s->execute([$email]); $u = $s->fetch();
    if (!$u || !password_verify($_POST['password'], $u['password'])) flash('Invalid email or password.', 'danger');
    elseif ($u['status'] != 'active') flash('Your account is deactivated. Contact admin.', 'danger');
    else {
        $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['name'], 'email' => $u['email'], 'role' => $u['role'], 'avatar' => $u['avatar']];
        log_action('login', $u['email']);
        redirect($u['role'] == 'user' ? 'index.php' : 'admin/index.php');
    }
}
include 'includes/header.php';
?>
<div class="auth-page">
  <aside class="auth-art">
    <span class="eyebrow"><span class="live-dot"></span> <?= SITE_NAME ?></span>
    <h2>Every good plan starts here.</h2>
    <p>Discover events, RSVP in one tap and keep track of everything you love.</p>
  </aside>
  <div class="auth-card">
    <h1>Welcome Back</h1>
    <p class="subtitle">Sign in to your <?= SITE_NAME ?> account</p>
    <?php show_flash(); ?>
    <form method="post">
      <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" value="<?= e($email) ?>" required>
      </div>
      <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
    </form>
    <p class="auth-link">Don't have an account? <a href="register.php">Create one</a></p>
    <p class="auth-note">Demo: admin@citypulse.pk / admin123<br>sara@citypulse.pk / user123</p>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
