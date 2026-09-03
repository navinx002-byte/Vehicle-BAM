<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
$message = ''; $type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $res = $conn->query("SELECT * FROM admins WHERE email='$email'");
    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $conn->query("UPDATE admins SET reset_token='$token', reset_token_expiry='$expiry' WHERE id={$user['id']}");
        $link = SITE_URL . "/customer/reset-password.php?token=$token&type=admin";
        if (sendResetEmail($email, $user['full_name'], $link)) {
            $message = "Password reset link sent to your email!"; $type = 'success';
        } else {
            $message = "Failed to send email. Please try again or reset manually."; $type = 'error';
        }
    } else {
        $message = "No admin account found with that email."; $type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password - Admin - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>const t=localStorage.getItem('vs_theme')||'light';document.documentElement.setAttribute('data-theme',t);</script>
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="logo-circle" style="background:linear-gradient(135deg,#1d3557,#457b9d);"><i class="fas fa-key"></i></div>
      <h2>Forgot Password</h2>
      <p>Admin password reset</p>
    </div>
    <?php if($message): ?><div class="alert alert-<?= $type ?>"><i class="fas fa-info-circle"></i> <?= $message ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Admin Email Address</label>
        <div class="input-icon"><i class="fas fa-envelope"></i>
          <input type="email" name="email" class="form-control" placeholder="Your admin email" required>
        </div>
      </div>
      <button type="submit" class="btn btn-dark" style="width:100%;justify-content:center;border-radius:10px;padding:14px;">
        <i class="fas fa-paper-plane"></i> Send Reset Link
      </button>
    </form>
    <div class="auth-footer" style="margin-top:15px;">
      <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Admin Login</a>
    </div>
  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>
