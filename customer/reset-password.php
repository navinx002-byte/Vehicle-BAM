<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
$type = isset($_GET['type']) ? $_GET['type'] : 'customer';
$token = isset($_GET['token']) ? $_GET['token'] : '';
$tables = ['customer'=>'customers','admin'=>'admins','mechanic'=>'mechanics'];
$table = $tables[$type] ?? 'customers';
$loginPages = ['customer'=>'login.php','admin'=>'../admin/login.php','mechanic'=>'../mechanic/login.php'];
$loginPage = $loginPages[$type] ?? 'login.php';
$user = null;
if ($token) {
    $res = $conn->query("SELECT * FROM $table WHERE reset_token='$token' AND reset_token_expiry > NOW()");
    if ($res->num_rows === 1) $user = $res->fetch_assoc();
}
$message = ''; $msgType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $pass = $_POST['password'];
    $cpass = $_POST['confirm_password'];
    if ($pass !== $cpass) { $message = "Passwords do not match!"; $msgType = 'error'; }
    elseif (strlen($pass) < 6) { $message = "Password must be at least 6 characters!"; $msgType = 'error'; }
    else {
        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        $id = $user['id'];
        $conn->query("UPDATE $table SET password='$hashed', reset_token=NULL, reset_token_expiry=NULL WHERE id=$id");
        setFlash('success', 'Password reset successfully! Please login.');
        header("Location: $loginPage"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>const t=localStorage.getItem('vs_theme')||'light';document.documentElement.setAttribute('data-theme',t);</script>
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="logo-circle"><i class="fas fa-lock"></i></div>
      <h2>Reset Password</h2>
      <p>Enter your new password below</p>
    </div>
    <?php if(!$user): ?>
      <div class="alert alert-error"><i class="fas fa-times-circle"></i> Invalid or expired reset link. Please request a new one.</div>
      <a href="forgot-password.php" class="btn btn-primary" style="width:100%;justify-content:center;">Request New Link</a>
    <?php else: ?>
      <?php if($message): ?><div class="alert alert-<?= $msgType ?>"><?= $message ?></div><?php endif; ?>
      <form method="POST">
        <div class="form-group">
          <label>New Password</label>
          <div class="input-icon">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" id="pass1" class="form-control" placeholder="Min 6 characters" required>
            <button type="button" class="toggle-password" onclick="togglePassword('pass1',this)"><i class="fas fa-eye"></i></button>
          </div>
        </div>
        <div class="form-group">
          <label>Confirm New Password</label>
          <div class="input-icon">
            <i class="fas fa-lock"></i>
            <input type="password" name="confirm_password" id="pass2" class="form-control" placeholder="Repeat password" required>
            <button type="button" class="toggle-password" onclick="togglePassword('pass2',this)"><i class="fas fa-eye"></i></button>
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;border-radius:10px;padding:14px;">
          <i class="fas fa-save"></i> Update Password
        </button>
      </form>
    <?php endif; ?>
  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>
