<?php
require 'includes/functions.php';
$page_title = 'Create Account'; $active = 'register'; $flash_inline = true;
if (is_logged()) redirect('index.php');
$name = $email = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']); $email = trim($_POST['email']); $pass = $_POST['password'];
    $s = $pdo->prepare("SELECT id FROM users WHERE email=?"); $s->execute([$email]);
    if (strlen($pass) < 6) flash('Password must be at least 6 characters.', 'danger');
    elseif ($pass !== ($_POST['confirm'] ?? '')) flash('Passwords do not match.', 'danger');
    elseif ($s->fetch()) flash('This email is already registered.', 'danger');
    else {
        $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)")
            ->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
        log_action('register', $email);
        flash('Account created! Please sign in.'); redirect('login.php');
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
    <h1>Create Account</h1>
    <p class="subtitle">Join <?= SITE_NAME ?> and discover amazing events</p>
    <?php show_flash(); ?>
    <form method="post">
      <div class="form-group">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" value="<?= e($name) ?>" required>
      </div>
      <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" value="<?= e($email) ?>" required>
      </div>
      <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Min. 6 characters" minlength="6" required>
      </div>
      <div class="form-group">
        <label for="confirm" class="form-label">Confirm Password</label>
        <input type="password" id="confirm" name="confirm" class="form-control" placeholder="Repeat your password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
    </form>
    <p class="auth-link">Already have an account? <a href="login.php">Sign in</a></p>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
